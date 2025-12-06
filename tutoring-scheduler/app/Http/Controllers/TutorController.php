<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function index()
    {
        $tutors = Tutor::all();
        return response()->json($tutors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tutors',
            'phone' => 'required|string',
            'bio' => 'nullable|string',
            'subjects' => 'required|array',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $tutor = Tutor::create($validated);
        return response()->json($tutor, 201);
    }

    public function show($id)
    {
        $tutor = Tutor::with('sessions')->findOrFail($id);
        return response()->json($tutor);
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:tutors,email,' . $id,
            'phone' => 'string',
            'bio' => 'nullable|string',
            'subjects' => 'array',
            'hourly_rate' => 'numeric|min:0',
        ]);

        $tutor->update($validated);
        return response()->json($tutor);
    }

    public function destroy($id)
    {
        $tutor = Tutor::findOrFail($id);
        $tutor->delete();
        return response()->json(null, 204);
    }
}