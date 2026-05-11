<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\BaseController;
use App\Models\Student;
use App\Models\Domain;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends BaseController
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(Request $request, $provider)
    {
        $request->validate([
            'domain_name' => 'required|string|exists:domains,domain_name',
        ]);

        // Keep domain name in state to know which organization the student is logging into
        $state = json_encode(['domain_name' => $request->domain_name]);

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->with(['state' => $state])->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Obtain the user information from the provider.
     */
    public function callback(Request $request, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Extract state
            $state = json_decode($request->input('state'), true);
            $domainName = $state['domain_name'] ?? null;
            
            if (!$domainName) {
                return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?error=' . urlencode('Domain name is missing in state.'));
            }

            $domain = Domain::where('domain_name', $domainName)->first();
            
            if (!$domain) {
                return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?error=' . urlencode('Invalid domain name.'));
            }

            // Find or create student
            $student = Student::where('organization_id', $domain->organization_id)
                ->where('provider_name', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$student) {
                // Check if a student with the same email already exists in this organization
                $student = Student::where('organization_id', $domain->organization_id)
                    ->where('email', $socialUser->getEmail())
                    ->first();

                if ($student) {
                    // Update existing student to include social credentials
                    $student->provider_name = $provider;
                    $student->provider_id = $socialUser->getId();
                    $student->save();
                } else {
                    // Create new student
                    $student = Student::create([
                        'organization_id' => $domain->organization_id,
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        // For username we can create a slug or just use email prefix
                        'username' => strtolower(explode('@', $socialUser->getEmail())[0]) . '_' . rand(1000, 9999),
                        'provider_name' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'password' => null, // Password is null for social login
                        'is_active' => true,
                    ]);
                }
            }

            if (!$student->is_active) {
                return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?error=' . urlencode('Student is not active.'));
            }

            $token = $student->createToken('student_auth', ['student'])->plainTextToken;

            // Prepare student info for frontend
            $studentData = json_encode([
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'username' => $student->username,
                'profile_picture' => $student->profile_picture,
            ]);

            return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?token=' . $token . '&student=' . urlencode($studentData));

        } catch (\Exception $e) {
            Log::error('Social Login Error: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback?error=' . urlencode('Authentication failed. Please try again.'));
        }
    }
}
