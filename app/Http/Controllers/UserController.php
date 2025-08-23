<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserMail;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = User::with('userRole','userDepartment')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadEmployeeComponent':
                return view('human-resource.partial.user-componenet', [
                    'employees' => $employees,
                ]);
            default:
                return view('human-resource.index', [
                    'employees' => $employees,
                ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();

        // Generate username and password
        $username = Str::lower($validatedData['first_name'] . ' ' .$validatedData['last_name']);       
        $randomPassword = Str::random(10);
        $hashedPassword = Hash::make($randomPassword);

        // Merge additional data into the validated array
        $userData = array_merge($validatedData, [
            'name' => $username,
            'password' => $hashedPassword,
        ]);

        try {
            // Create user
            $user = User::create($userData);

            $role = Role::find($request->role_id);
            
            if ($user) {
                $user->assignRole($role->name);

                Mail::to($user->email)->send(new NewUserMail(
                    $user->first_name . ' ' . $user->last_name,
                    $user->userRole->name ?? 'No Role Assigned',  
                    $user->departmentName->name ?? 'Restaurant', 
                    $user->email,
                    $randomPassword
                ));

                // \Log::info($user->email);

            }

            return response()->json([
                'success' => true,
                'reload' => true,
                'componentId' => 'reloadEmployeeComponent',
                'refresh' => false,
                'message' => __('auth._created'),
                'redirect' => route('employee.index'),
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => __('auth.user_exist'),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        // Get the validated data
        $validatedData = $request->validated();

        // Get the authenticated user
        $authUser = auth()->user();

        // Check if the authenticated user has the required role
        if ($authUser && in_array($authUser->role_id, [1])) {
            // Find the user by ID
            $user = User::find($id);

            // If the user is found, update their details
            if ($user) {
                // Update user details
                $user->update($validatedData);

                // Synchronize roles (if role has changed)
                if (isset($validatedData['role_id'])) {
                    $role = Role::find($validatedData['role_id']);
                    $user->syncRoles($role->name);
                }

                // Return success response
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'componentId' => 'reloadEmployeeComponent',
                    'refresh' => false,
                    'message' => __('auth._updated'),
                    'redirect' => route('employee.index'),
                ]);
            }

            // If user not found, return failure response
            return response()->json([
                'success' => false,
                'message' => __('auth._not_found'),
            ]);
        }

        // If authenticated user doesn't have the required role
        return response()->json([
            'success' => false,
            'message' => __('auth.does_not_permision'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        // Get the authenticated user
        $authUser = auth()->user();

        if ($authUser && in_array($authUser->role_id, [1])) { 
            $user->delete();
            
            return response()->json([
                'success' => true,
                'reload' => true,
                'componentId' => 'reloadEmployeeComponent',
                'refresh' => false,
                'message' => __('auth._deleted'),
                'redirect' => route('employee.index'),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __('auth._undeleletable'),
        ]);
        
    }

    public function changeEmployeeStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',  // Ensures only 'active' or 'inactive' are allowed
        ]);
    
        // Find the user by ID
        $user = User::find($id);
        if ($user->userRole->name == 'admin') {
            return response()->json([
                'success' => false,
                'message' => __('auth._user_not_updatable'),
            ]);
        }
    
        // Check if the user exists and update their status
        if ($user) {
            $user->status = $validated['status'];  // Directly update the status field
            if ($user->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'reloadEmployeeComponent',
                    'message' => __('auth._updated'),
                ]);
            }
        }
    
        // If user not found or status update failed
        return response()->json([
            'success' => false,
            'message' => __('auth._not_found'),
        ]);
    }

    public function updateDepartments(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        // validate departments[] array
        $validated = $request->validate([
            'departments'   => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
        ]);

        // Sync departments (many-to-many relation)
        $employee->departments()->sync($validated['departments'] ?? []);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('auth._updated'),
        ]);

        return redirect()->back();
    }


}
