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
use App\Services\GmailService;

class StudentAuthController extends BaseController
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

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

            // Check username uniqueness within organization
            if (Student::where('organization_id', $domain->organization_id)
                ->where('username', $request->username)
                ->exists()) {
                return response()->json(['error' => 'Username already taken'], 422);
            }

            // Check email uniqueness within organization if email is provided
            if ($request->email && Student::where('organization_id', $domain->organization_id)
                ->where('email', $request->email)
                ->exists()) {
                return response()->json(['error' => 'Email already taken'], 422);
            }

            $student = Student::create([
                'organization_id' => $domain->organization_id,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'password' => bcrypt($request->password),
                'is_active' => true,
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
            ->where(function($query) use ($request){
                $query->where('username', $request->username)
                    ->orWhere('email', $request->username);
            })
            ->first();

        if(!$student){
            return $this->error('Invalid credentials', ['error' => 'Invalid credentials']);
        }

        if(!$student->is_active){
            return $this->error('Student is not active', ['error' => 'Student is not active']);
        }

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
            $student = $request->user('student');
            
            $request->validate([
                'name' => 'required|string',
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) use ($student) {
                        // Check if email is unique within the same organization, excluding current student
                        if (Student::where('organization_id', $student->organization_id)
                            ->where('email', $value)
                            ->where('id', '!=', $student->id)
                            ->exists()) {
                            $fail('The email has already been taken in this organization.');
                        }
                    },
                ],
                'username' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) use ($student) {
                        if ($value) {
                            // Check if username is unique within the same organization, excluding current student
                            if (Student::where('organization_id', $student->organization_id)
                                ->where('username', $value)
                                ->where('id', '!=', $student->id)
                                ->exists()) {
                                $fail('The username has already been taken in this organization.');
                            }
                        }
                    },
                ],
            ]);

            $input = $request->all();
            if($request->hasFile('profile_picture')){
                $folder = $student->organization_id.'/profile_pictures';

                if ($student->profile_picture) {
                    Storage::disk('r2')->delete($folder . '/' . $student->profile_picture);
                }

                $profilen_name = time().'.'.$request->file('profile_picture')->getClientOriginalExtension();
                $request->file('profile_picture')->storeAs($folder, $profilen_name, 'r2');
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

    /* forgot password */
    public function forgot_password(Request $request){
        try{
            $request->validate([
                'domain_name' => 'required|string|exists:domains',
                'email' => 'required|email',
            ]);
            $domain = Domain::where('domain_name', $request->domain_name)->firstOrFail();

            $student = Student::where('organization_id', $domain->organization_id)
                ->where(function($query) use ($request){
                    $query->where('email', $request->email);
                })
                ->first();
            if(!$student){
                return $this->error('Student not found', ['error' => 'Student not found']);
            }
            $token = Password::broker('students')->createToken($student);
            $this->gmailService->send(
                $student->email,
                'Password Reset',
                'Click the link to reset your password: ' . env('STUDENT_PASSWORD_RESET_URL') . '/reset-password?token=' . $token.'&email=' . $student->email,
            );
            return $this->success('Password reset token sent successfully', ['message' => 'Password reset link has been sent to your email']);
        }
        catch(\Exception $e){
            return $this->error('Forgot password failed', ['error' => $e->getMessage()]);
        }
    }

    /* reset password using token */
    public function reset_password_using_token(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $status = Password::broker('students')->reset(
                [
                    'email' => $request->email,
                    'token' => $request->token,
                    'password' => $request->new_password,
                    'password_confirmation' => $request->new_password_confirmation,
                ],
                function ($student, $password) {
                    $student->forceFill(['password' => Hash::make($password)])->save();
                }
            );

            if ($status !== Password::PASSWORD_RESET) {
                if ($status === Password::INVALID_USER) {
                    return $this->error('Student not found', ['error' => 'No student found with this email.']);
                }
                if ($status === Password::INVALID_TOKEN) {
                    return $this->error('Invalid or expired token', ['error' => 'The reset link is invalid or has expired. Request a new one.']);
                }
                return $this->error('Password reset failed', ['error' => 'Unable to reset password.']);
            }

            $student = Student::where('email', $request->email)->first();
            return $this->success('Password reset successfully', ['student' => $student]);
        } catch (\Exception $e) {
            return $this->error('Password reset failed', ['error' => $e->getMessage()]);
        }
    }
}
