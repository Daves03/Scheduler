<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TutoringSession;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // GET /api/bookings
   // GET /api/bookings
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. ADMIN: See Everything
        if ($user->role === 'admin') {
            return Booking::with(['session.tutor'])->get();
        }

        // 2. TUTOR: See bookings for THEIR sessions only
        if ($user->role === 'tutor') {
            // Find the Tutor profile that has this user's email
            $tutorProfile = \App\Models\Tutor::where('email', $user->email)->first();
            
            if (!$tutorProfile) return []; // If no profile found, return empty

            // Get bookings where the session belongs to this tutor
            return Booking::with(['session.tutor'])
                ->whereHas('session', function($q) use ($tutorProfile) {
                    $q->where('tutor_id', $tutorProfile->id);
                })
                ->get();
        }

        // 3. STUDENT: See ONLY their own bookings
        return Booking::with(['session.tutor'])
            ->where('student_email', $user->email)
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:tutoring_sessions,id',
            'student_name' => 'required|string|max:255',
            'student_email' => 'required|email',
            'student_phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // 🔒 PRIVACY FILTER 2: CREATING
        // If a Student tries to book, force the email to be their own.
        // This stops them from booking on behalf of "admin@test.com" or other users.
        if ($request->user()->role !== 'admin') {
            $validated['student_email'] = $request->user()->email;
        }

        $session = TutoringSession::findOrFail($validated['session_id']);

        if ($session->status !== 'available') {
            return response()->json(['error' => 'Session is not available'], 400);
        }

        $booking = Booking::create($validated);
        $session->update(['status' => 'booked']);

        return response()->json($booking->load('session.tutor'), 201);
    }

    public function show($id, Request $request)
    {
        $booking = Booking::with(['session.tutor'])->findOrFail($id);

        // 🔒 PRIVACY FILTER 3: VIEWING SINGLE
        // Check if user owns this booking or is admin
        if ($request->user()->role !== 'admin' && $booking->student_email !== $request->user()->email) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // 🔒 PRIVACY FILTER 4: UPDATING
        if ($request->user()->role !== 'admin' && $booking->student_email !== $request->user()->email) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'in:pending,confirmed,cancelled',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'cancelled') {
            $booking->session->update(['status' => 'available']);
        }

        $booking->update($validated);
        return response()->json($booking->load('session.tutor'));
    }

    public function destroy(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // 🔒 PRIVACY FILTER 5: DELETING
        // Only allow delete if Admin OR if the booking belongs to the User
        if ($request->user()->role !== 'admin' && $booking->student_email !== $request->user()->email) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->session->update(['status' => 'available']);
        $booking->delete();
        return response()->json(null, 204);
    }
}