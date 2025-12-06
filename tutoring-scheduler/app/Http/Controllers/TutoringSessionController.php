<?php

namespace App\Http\Controllers;

use App\Models\TutoringSession;
use Illuminate\Http\Request;

class TutoringSessionController extends Controller
{
public function index(Request $request)
{
    // ✅ Allow everyone (Admins, Tutors, Students) to see ALL sessions
    // This fixes the "Available Sessions" tab for Tutors
    $query = TutoringSession::with(['tutor', 'booking']);

    if ($request->has('tutor_id')) {
        $query->where('tutor_id', $request->tutor_id);
    }

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    $sessions = $query->orderBy('session_date')
                      ->orderBy('start_time')
                      ->get();

    return response()->json($sessions);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            // We removed 'tutor_id' from required because we might auto-fill it
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject' => 'required|string',
        ]);

        $user = $request->user();

        // 🔒 IF TUTOR: Force the ID to be their own
        if ($user->role === 'tutor') {
            $tutorProfile = \App\Models\Tutor::where('email', $user->email)->first();
            if (!$tutorProfile) {
                return response()->json(['message' => 'Tutor profile not found'], 404);
            }
            $validated['tutor_id'] = $tutorProfile->id;
        } else {
            // If Admin, they must provide the ID
            $request->validate(['tutor_id' => 'required|exists:tutors,id']);
            $validated['tutor_id'] = $request->tutor_id;
        }

        $validated['status'] = 'available';

        $session = TutoringSession::create($validated);
        return response()->json($session->load('tutor'), 201);
    }

    public function show($id)
    {
        $session = TutoringSession::with(['tutor', 'booking'])->findOrFail($id);
        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $session = TutoringSession::findOrFail($id);
        
        $validated = $request->validate([
            'tutor_id' => 'exists:tutors,id',
            'session_date' => 'date',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'subject' => 'string',
            'status' => 'in:available,booked,completed,cancelled',
        ]);

        $session->update($validated);
        return response()->json($session->load('tutor'));
    }

    public function destroy($id)
    {
        $session = TutoringSession::findOrFail($id);
        $session->delete();
        return response()->json(null, 204);
    }
}