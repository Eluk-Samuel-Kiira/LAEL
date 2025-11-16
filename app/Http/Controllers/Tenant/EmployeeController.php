<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Employee };

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = Employee::latest()->get();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'employeeUserIndexTable':
                return view('department.employee.component', [
                    'employees' => $employees,
                ]);
            default:
                return view('department.employee-index', [
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        try {
            \Log::info('Uou');
            // Find the employee
            $employee = Employee::findOrFail($id);
            
            // Validate the request
            $validated = $request->validate([
                'salary' => 'required|numeric|min:0',
                'salary_type' => 'required|in:hourly,weekly,monthly,annual',
                'hire_date' => 'required|date',
                'termination_date' => 'nullable|date',
            ]);

            // Update employee fields
            $employee->update([
                'salary' => $validated['salary'],
                'salary_type' => $validated['salary_type'],
                'hire_date' => $validated['hire_date'],
                'termination_date' => $validated['termination_date'],
                'is_active' => $validated['termination_date'] ? false : true,
            ]);

            // Sync with User model if user exists
            if ($employee->user) {
                \Log::info($employee->user);
                $employee->user->update([
                    'job_title' => $employee->job_title, 
                    'first_name' => $employee->first_name, 
                    'last_name' => $employee->last_name, 
                    'email' => $employee->email, 
                    'phone' => $employee->telephone_number, 
                    'department_id' => $employee->department_id, 
                ]);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'reload' => true,
                'componentId' => 'employeeUserIndexTable',
                'refresh' => false,
                'message' => __('auth._updated'),
                'redirect' => route('user.index'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function changeUserStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
    
        // Find the user by ID
        $user = Employee::find($id);
    
        // Check if the user exists and update their status
        if ($user) {
            $user->is_active = $validated['status'];  // Directly update the status field
            if ($user->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'componentId' => 'employeeUserIndexTable',
                    'refresh' => false,
                    'message' => __('auth._updated'),
                    'redirect' => route('user.index'),
                ]);
            }
        }
    
        // If user not found or status update failed
        return response()->json([
            'success' => false,
            'message' => __('auth._not_found'),
        ]);
    }
}
