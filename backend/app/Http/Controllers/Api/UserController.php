<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:profiles,username,' . ($user->profile->profile_id ?? 'NULL') . ',profile_id',
            'recipient_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'label' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_day' => 'nullable|integer|min:1|max:31',
            'birth_month' => 'nullable|integer|min:1|max:12',
            'birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'profile_picture' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user name
        $user->update(['name' => $request->name]);

        // Prepare profile data
        $profileData = [
            'username' => $request->username,
            'recipient_name' => $request->recipient_name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'label' => $request->label,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_day' => $request->birth_day,
            'birth_month' => $request->birth_month,
            'birth_year' => $request->birth_year,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profileData['profile_picture'] = $path;
        }

        // Update or create profile
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            Profile::create($profileData);
        }

        $user->refresh();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Request OTP for email change
     */
    public function requestEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if ($request->new_email == $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email baru harus berbeda dari email saat ini'
            ], 400);
        }

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Store OTP in session or database (for simplicity, using user model)
        $user->update(['otp' => $otp]);

        // Send OTP to current email
        try {
            Mail::raw("Kode OTP untuk mengubah email Anda: $otp", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Ubah Email');
            });

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Verify OTP and update email
     */
    public function verifyEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if ($request->otp != $user->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah'
            ], 400);
        }

        // Update email
        $user->update([
            'email' => $request->new_email,
            'otp' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diubah',
            'data' => $user
        ]);
    }

    /**
     * Request OTP for phone change
     */
    public function requestPhoneOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_phone' => 'required|numeric|digits_between:10,15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if ($request->new_phone == $user->profile?->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon baru harus berbeda dari nomor saat ini'
            ], 400);
        }

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Store OTP
        $user->update(['otp' => $otp]);

        // Send OTP to email (since we can't send SMS)
        try {
            Mail::raw("Kode OTP untuk mengubah nomor telepon Anda: $otp", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Ubah Nomor Telepon');
            });

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Verify OTP and update phone
     */
    public function verifyPhoneOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_phone' => 'required|numeric|digits_between:10,15',
            'otp' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if ($request->otp != $user->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah'
            ], 400);
        }

        // Update or create profile with new phone
        if ($user->profile) {
            $user->profile->update(['phone' => $request->new_phone]);
        } else {
            Profile::create([
                'user_id' => $user->id,
                'phone' => $request->new_phone,
                'recipient_name' => $user->name,
                'address' => '',
                'label' => 'Rumah'
            ]);
        }

        // Clear OTP
        $user->update(['otp' => null]);

        $user->refresh();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Nomor telepon berhasil diubah',
            'data' => $user
        ]);
    }

    /**
     * Update email without OTP (simple update)
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 400);
        }

        $user->update(['email' => $request->new_email]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diubah',
            'data' => $user
        ]);
    }

    /**
     * Update phone without OTP (simple update)
     */
    public function updatePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_phone' => 'required|numeric|digits_between:10,15',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 400);
        }

        // Update or create profile with new phone
        if ($user->profile) {
            $user->profile->update(['phone' => $request->new_phone]);
        } else {
            Profile::create([
                'user_id' => $user->id,
                'phone' => $request->new_phone,
                'recipient_name' => $user->name,
                'address' => '',
                'label' => 'Rumah'
            ]);
        }

        $user->refresh();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'Nomor telepon berhasil diubah',
            'data' => $user
        ]);
    }
}
