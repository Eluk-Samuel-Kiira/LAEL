<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departments = Department::latest()->get();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'departmentIndexTable':
                return view('department.partials.department-component', [
                    'departments' => $departments,
                ]);
            default:
                return view('department.department-index', [
                    'departments' => $departments,
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
        $request->validate([
            'name' => 'required|string|max:55|unique:departments,name',
            'manager_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Check maximum departments limit
        $currentDepartmentCount = Department::where('tenant_id', $tenantId)->count();
        $maxDepartments = tenant_setting($tenantId, 'max_departments');

        if ($currentDepartmentCount >= $maxDepartments) {
            return response()->json([
                'success' => false,
                'message' => __('auth.maximum_departments_reached', ['max' => $maxDepartments]),
            ]);
        }

        $department = Department::create([
            'name' => $request->name,
            'created_by' => $user->id,
            'manager_id' => $request->manager_id,
            'tenant_id' => $tenantId,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'departmentIndexTable',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('department.index'),
        ]);
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
        $department = Department::find($id);
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('departments', 'name')->ignore($department->id),
            ],
            'manager_id' => 'required|exists:users,id',
        ]);

        $department->update([
            'name' => $request->name,
            'created_by' => Auth::user()->id,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'departmentIndexTable',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('department.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $department = Department::find($id);
        if ($department->isActive === 1) {
            return response()->json([
                'success' => false,
                'message' => __('This Department Is Still Active!'),
            ]);
        }

        $department->delete();return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'departmentIndexTable',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('department.index'),
        ]);
    }

    public function changeDepartmentStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
    
        // Find the user by ID
        $department = Department::find($id);
    
        if ($department) {
            $department->isActive = $validated['status']; 
            if ($department->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'departmentIndexTable',
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
}
