<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display users with search and filters.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search by name or email
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Filter by account status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers'
        ));
    }

    /**
     * Activate or deactivate a user.
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Protect the currently logged-in administrator
        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'You cannot deactivate your own account.'
            );
        }

        // Prevent changing another administrator's status here
        if ($user->hasRole('admin')) {
            return back()->with(
                'error',
                'Administrator accounts cannot be changed from this screen.'
            );
        }

        $user->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'User account status updated successfully.'
        );
    }
}