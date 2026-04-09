<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed(); // include deleted users

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->paginate(5);

        return view('users.index', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'User soft deleted successfully!');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return back()->with('success', 'User restored successfully!');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->trashed()) {
            return back()->with('success', 'Cannot change status of a deleted user!');
        }

        $user->status = !$user->status;
        $user->save();
        return back()->with('success', 'User status updated successfully!');
    }

    // Export users CSV
    public function export()
    {
        $filename = 'users.csv';
        $users = User::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // Header row
            fputcsv($file, ['ID', 'Name', 'Email', 'Status', 'Created At']);
            // User rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->status ? 'Active' : 'Inactive',
                    $user->created_at,
                ]);
            }
            fclose($file);
        };

        // Pass headers directly in the third argument
        return response()->stream($callback, 200, $headers);
    }
}