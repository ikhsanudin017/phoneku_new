<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends BaseAdminController
{
    public function index(Request $request)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $query = User::query()->with(['profile', 'orders']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('profile', function($q) use ($search) {
                          $q->where('phone', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Add statistics
            $query->withCount(['orders', 'cart as cart_items_count']);

            // Pagination
            $users = $query->latest()->paginate($request->per_page ?? 10);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        if ($response = $this->validate($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:customer,admin',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive'
        ])) {
            return $response;
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status ?? 'active'
            ]);

            if ($request->filled('phone')) {
                Profile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'recipient_name' => $user->name,
                    'label' => 'Default',
                    'address' => '',
                ]);
            }

            DB::commit();

            $user->load(['profile', 'orders']);
            return $this->successResponse($user, 'User created successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        if ($response = $this->validate($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:customer,admin',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive'
        ])) {
            return $response;
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->filled('status')) {
                $updateData['status'] = $request->status;
            }

            $user->update($updateData);

            if ($request->filled('phone')) {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $request->phone,
                        'recipient_name' => $user->name,
                        'label' => 'Default',
                        'address' => $user->profile->address ?? '',
                    ]
                );
            }

            DB::commit();

            $user->load(['profile', 'orders']);
            return $this->successResponse($user, 'User updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        // Don't allow deleting self
        if ($user->id_user === auth()->id_user) {
            return $this->errorResponse('Cannot delete your own account');
        }

        try {
            DB::beginTransaction();

            // Delete related records
            $user->profile()->delete();
            $user->orders()->delete();
            $user->cart()->delete();
            $user->sentMessages()->delete();
            $user->receivedMessages()->delete();
            $user->delete();

            DB::commit();

            return $this->successResponse(null, 'User deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete user: ' . $e->getMessage());
        }
    }
}
