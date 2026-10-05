<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is disabled',
            ], 403);
        }

        $school = $user->school;
        
        if ($school && $school->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'School account is not active',
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $user->photo,
                    'avatar_url' => $user->avatar_url,
                    'is_verified' => $user->is_verified,
                    'role' => $user->getRoleNames()->first(),
                ],
                'school' => $school ? [
                    'id' => $school->id,
                    'name' => $school->name,
                    'slug' => $school->slug,
                    'logo' => $school->logo,
                ] : null,
                'token' => $token,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $school = null;
        if ($request->school_id) {
            $school = School::find($request->school_id);
            if (!$school || $school->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid school',
                ], 400);
            }
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'school_id' => $request->school_id,
            'is_active' => true,
        ]);

        $user->assignRole('student');

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $school = $user->school;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $user->photo,
                    'avatar_url' => $user->avatar_url,
                    'is_verified' => $user->is_verified,
                    'email_verified_at' => $user->email_verified_at,
                    'role' => $user->getRoleNames()->first(),
                    'permissions' => $user->getPermissionNames(),
                ],
                'school' => $school ? [
                    'id' => $school->id,
                    'name' => $school->name,
                    'slug' => $school->slug,
                    'logo' => $school->logo,
                ] : null,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['first_name', 'last_name', 'phone']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profiles', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $user->photo,
                    'avatar_url' => $user->avatar_url,
                ],
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        $user->update([
            'password' => $request->password,
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => ['token' => $token],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $token = str.random(64);
        
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // Send email (implement notification)
        // $user->sendPasswordResetNotification($token);

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !hash_verifY('sha256', $request->token, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token',
            ], 400);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            return response()->json([
                'success' => false,
                'message' => 'Reset token expired',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);

        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful',
        ]);
    }

    public function verifyTwoFactor(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!$user->otp_code || $user->otp_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code',
            ], 400);
        }

        if ($user->otp_expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code expired',
            ], 400);
        }

        $user->clearOTP();
        $user->markAsVerified();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor verification successful',
        ]);
    }
}