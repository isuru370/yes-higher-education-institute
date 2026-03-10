<?php

namespace App\Services;

use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\DB;

class SystemUserService
{
    // Return all system users
    public function getSystemUsers()
    {
        try {
            $users = SystemUser::with(['user', 'user.userType'])
                ->where('custom_id', '!=', 'ADM001') // skip the main admin
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Return one system user
    public function getSystemUser($id)
    {
        try {
            $user = SystemUser::with(['user', 'user.userType'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }


    // Create a system user
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required',
                'password' => 'required|min:6',
                'user_type' => 'required|exists:user_types,id'
            ]);

            $customId = $this->generateCustomId();

            // Create main user (Laravel default 'users' table)
            $user = User::create([
                'name' => $request->fname . ' ' . $request->lname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'is_active' => 1
            ]);

            // Create system user
            $systemUser = SystemUser::create([
                'custom_id' => $customId,
                'user_id' => $user->id,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'nic' => $request->nic,
                'bday' => $request->bday,
                'gender' => $request->gender,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => 1
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'System user created successfully',
                'data' => $systemUser
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create system user',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Update system user
    public function update(Request $request, $id)
    {
        try {
            $systemUser = SystemUser::with('user')->findOrFail($id);

            $request->validate([
                'email' => [
                    'sometimes',
                    'email',
                    Rule::unique('users', 'email')->ignore($systemUser->user_id),
                    Rule::unique('system_users', 'email')->ignore($id)
                ],
                'password' => 'sometimes|min:6'
            ]);

            // Update user table
            $userData = [];

            if ($request->filled('fname') || $request->filled('lname')) {
                $userData['name'] = trim($request->fname . ' ' . $request->lname);
            }

            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }

            if ($request->filled('user_type')) {
                $userData['user_type'] = $request->user_type;
            }

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if (!empty($userData)) {
                $systemUser->user->update($userData);
            }

            // Update system_users table
            $systemUser->update(
                $request->except(['password', 'user_type'])
            );

            return response()->json([
                'status' => 'success',
                'message' => 'System user updated successfully',
                'data' => $systemUser
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update system user',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    // Soft deactivate
    public function destroy($id)
    {
        try {
            $user = SystemUser::with('user')->findOrFail($id);

            $user->update(['is_active' => 0]);
            $user->user->update(['is_active' => 0]);

            return response()->json([
                'status' => 'success',
                'message' => 'User deactivated'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate user',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Reactivate
    public function reactivate($id)
    {
        try {
            $user = SystemUser::with('user')->findOrFail($id);

            $user->update(['is_active' => 1]);
            $user->user->update(['is_active' => 1]);

            return response()->json([
                'status' => 'success',
                'message' => 'User reactivated'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate user',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /* ------------------------------------------------------------
     | UTILITIES
     |------------------------------------------------------------ */

    private function generateCustomId()
    {
        // Get the last system user by ID (not by custom_id)
        $lastUser = SystemUser::orderBy('id', 'desc')->first();

        if (!$lastUser) {
            // If no users exist, start from SAE0001
            return 'ADM0001';
        }

        // Extract the numeric part from the last custom_id
        $lastCustomId = $lastUser->custom_id;

        // Remove 'SAE' prefix and get the number
        $lastNumber = (int) substr($lastCustomId, 3);

        // Increment by 1
        $nextNumber = $lastNumber + 1;

        // Format with leading zeros (4 digits)
        return 'ADM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
