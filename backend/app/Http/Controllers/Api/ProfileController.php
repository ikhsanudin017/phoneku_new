<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $user->load('profile');
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:profiles,username,' . ($user->profile->id ?? 'NULL') . ',id',
                'recipient_name' => 'required|string|max:255',
                'address' => 'required|string',
                'phone' => 'required|string|max:15',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user name
            $user->update([
                'name' => $request->name
            ]);

            // Handle profile picture
            if ($request->hasFile('profile_picture')) {
                if ($user->profile && $user->profile->profile_picture) {
                    Storage::disk('public')->delete($user->profile->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            // Update or create profile
            $profileData = [
                'username' => $request->username,
                'recipient_name' => $request->recipient_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ];

            if (isset($path)) {
                $profileData['profile_picture'] = $path;
            }

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }

            DB::commit();

            $user->load('profile');

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update profile', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    public function getOrders()
    {
        try {
            $user = Auth::user();
            $orders = $user->orders()->with(['items.product'])->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }
}
