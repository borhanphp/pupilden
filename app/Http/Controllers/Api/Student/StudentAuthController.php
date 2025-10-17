<?php
namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\BaseController;
use App\Models\Student;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetLink;

class StudentAuthController extends BaseController
{
    public function register(Request $request)
    {
        try{
            $request->validate([
                'domain_name' => 'required|string|exists:domains,domain_name',
                'username' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
                'name' => 'nullable|string',
                'email' => 'nullable|email'
            ]);

            $domain = Domain::where('domain_name', $request->domain_name)->firstOrFail();

            if (Student::where('organization_id', $domain->organization_id)
                ->where('username', $request->username)
                ->exists()) {
                return response()->json(['error' => 'Username already taken'], 422);
            }

            $student = Student::create([
                'organization_id' => $domain->organization_id,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'password' => bcrypt($request->password),
            ]);

            return $this->success('Student created successfully', ['student' => $student]);
        }catch(\Exception $e){
            return $this->error('Student creation failed', ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'domain_name' => 'required|string|exists:domains',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $domain = Domain::where('domain_name', $request->domain_name)->firstOrFail();

        $student = Student::where('organization_id', $domain->organization_id)
            ->where('username', $request->username)
            ->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $student->createToken('student_auth', ['student'])->plainTextToken;

        return $this->success('Student logged in successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'student' => $student,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user('student'));
    }

    public function logout(Request $request)
    {
        $request->user('student')->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function profile_update(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email'
            ]);

            $student = $request->user('student');
            $input = $request->all();
            if($request->hasFile('profile_picture')){
                $folder = $student->organization_id.'/profile_pictures';
                if(!Storage::disk('public')->exists($folder)){
                    Storage::disk('public')->makeDirectory($folder);
                    chmod(Storage::disk('public')->path($folder), 0755);
                }

                $profilen_name = time().'.'.$request->file('profile_picture')->getClientOriginalExtension();
                $request->file('profile_picture')->storeAs($folder, $profilen_name, 'public');
                $input['profile_picture'] = $profilen_name;
            }
            
            $student->update($input);
        
            return $this->success('Student profile', ['student' => $student]);
        }catch(\Exception $e){
            return $this->error('Student profile update failed', ['error' => $e->getMessage()]);
        }
    }

    public function change_password(Request $request)
    {
        try{
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);
            $student = $request->user('student');
            if(!Hash::check($request->old_password, $student->password)){
                return $this->error('Old password is incorrect', ['error' => 'Old password is incorrect']);
            }
            $student->password = Hash::make($request->new_password);
            $student->save();
            return $this->success('Password changed successfully', ['student' => $student]);
        }
        catch(\Exception $e){
            return $this->error('Password change failed', ['error' => $e->getMessage()]);
        }
    }

    public function reset_password(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email',
            ]);
            $student = Student::where('email', $request->email)->first();
            if(!$student){
                return $this->error('Student not found', ['error' => 'Student not found']);
            }
            $student->password = Hash::make($request->new_password);
            $student->save();
            return $this->success('Password reset successfully', ['student' => $student]);
        }catch(\Exception $e){
            return $this->error('Password reset failed', ['error' => $e->getMessage()]);
        }
    }

    /* send password reset tockern to email */
    public function send_password_reset_token(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email',
            ]);
            $student = Student::where('email', $request->email)->first();
            if(!$student){
                return $this->error('Student not found', ['error' => 'Student not found']);
            }
            $token = Password::createToken($student);
            Mail::to($student->email)->send(new PasswordResetLink($token, $student));
            return $this->success('Password reset token sent successfully', ['message' => 'Password reset link has been sent to your email']);
        }
        catch(\Exception $e){
            return $this->error('Password reset token send failed', ['error' => $e->getMessage()]);
        }
    }
}
