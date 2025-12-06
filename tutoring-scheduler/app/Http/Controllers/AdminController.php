<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tutor;
use App\Models\TutoringSession;
use App\Models\Booking;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users'    => User::count(),
            'total_tutors'   => Tutor::count(),
            'total_sessions' => TutoringSession::count(),
            'total_bookings' => Booking::count(),
        ]);
    }

    // GET USERS (Active & Deleted)
    public function index(Request $request)
    {
        // Check if we want to see the trash bin
        if ($request->has('trash') && $request->trash == 'true') {
            // Return ONLY deleted users
            return User::onlyTrashed()->get();
        }

        // Return ONLY active users
        return User::all();
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'role']));
        return response()->json(['message' => 'User updated']);
    }

    // SOFT DELETE
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (auth()->id() == $id) return response()->json(['message' => 'Cannot delete self'], 400);

        // 1. If Tutor, soft delete their profile too (Removes from Browse)
        if ($user->role === 'tutor') {
            Tutor::where('email', $user->email)->delete();
        }

        // 2. Soft delete the Login
        $user->delete();

        return response()->json(['message' => 'User moved to trash']);
    }

    // RESTORE (Bring back from Trash)
    public function restore($id)
    {
        // 1. Restore User Login
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // 2. If Tutor, Restore Profile (Appears in Browse again)
        if ($user->role === 'tutor') {
            Tutor::withTrashed()->where('email', $user->email)->restore();
        }

        return response()->json(['message' => 'User restored successfully']);
    }
}