<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    // List salaries (with search & pagination) - Admin only
    public function index(Request $request)
{
    $user = Auth::user();
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $query = Salary::query();

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $perPage = $request->input('per_page', 10);
    $paginated = $query->paginate($perPage);

    // Apply default commission and compute displayed_salary
    $paginated->getCollection()->transform(function ($salary) {
        $salary->commission = $salary->commission ?? 500;
        $salary->displayed_salary = ($salary->salary_euros ?? 0) + $salary->commission;
        return $salary;
    });

    return response()->json($paginated);
}


    // Show salary by email - Admins can view any, users only their own
    public function showByEmail($email)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->email !== $email) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $salary = Salary::where('email', $email)->first();

        if (!$salary) {
            return response()->json(['error' => 'Salary record not found'], 404);
        }

        // Add displayed_salary field
        $salary->displayed_salary = ($salary->salary_euros ?? 0) + ($salary->commission ?? 0);

        return response()->json($salary);
    }

    // Create or update salary by email (role-based rules)
    public function storeOrUpdateByEmail(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email'],
            'salary_local'  => 'required|numeric|min:0',
            'salary_euros'  => 'nullable|numeric|min:0',
            'commission'    => 'nullable|numeric|min:0',
        ], [
            'name.required' => 'Name is required.',
            'email.email'   => 'Please provide a valid email address.',
            'salary_local.required' => 'Local salary is required.',
        ]);

        // Normal users can only edit their own email
        if ($user->role !== 'admin') {
            if ($validated['email'] !== $user->email) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Keep existing euros & commission for normal users
            $existing = Salary::where('email', $validated['email'])->first();
            $salary_euros = $existing->salary_euros ?? 0;
            $commission   = $existing->commission ?? 500;

            $data = [
                'name'         => $validated['name'],
                'salary_local' => $validated['salary_local'],
                'salary_euros' => $salary_euros,
                'commission'   => $commission,
            ];
        }
        // Admin can create/update any record
        else {
            $data = [
                'name'         => $validated['name'],
                'salary_local' => $validated['salary_local'],
                'salary_euros' => $validated['salary_euros'] ?? 0,
                'commission'   => $validated['commission'] ?? 500,
            ];
        }

        $salary = Salary::updateOrCreate(
            ['email' => $validated['email']],
            $data
        );

        // Add displayed_salary in response
        $salary->displayed_salary = ($salary->salary_euros ?? 0) + ($salary->commission ?? 0);

        return response()->json($salary);
    }

    // Update salary by ID - Admin only
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $salary = Salary::find($id);
        if (!$salary) {
            return response()->json(['error' => 'Salary record not found'], 404);
        }

        $validated = $request->validate([
            'salary_local'  => 'nullable|numeric|min:0',
            'salary_euros'  => 'nullable|numeric|min:0',
            'commission'    => 'nullable|numeric|min:0',
        ]);

        $salary->update($validated);

        // Add displayed_salary
        $salary->displayed_salary = ($salary->salary_euros ?? 0) + ($salary->commission ?? 0);

        return response()->json($salary);
    }

    // Delete salary by ID - Admin only
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $salary = Salary::find($id);
        if (!$salary) {
            return response()->json(['error' => 'Salary record not found'], 404);
        }

        $salary->delete();

        return response()->json(['message' => 'Salary record deleted successfully']);
    }
}
