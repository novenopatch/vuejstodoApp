<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // Récupérer tous les todos
    public function index(): JsonResponse
    {
        return response()->json(Todo::all(), 200);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'title' => $validated['title'],
            'completed' => $request->input('completed', false),
        ]);

        return response()->json($todo, 201);
    }


    public function show($id): JsonResponse
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        return response()->json($todo, 200);
    }


    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'completed' => 'nullable|boolean',
        ]);

        $todo->update($validated);

        return response()->json($todo, 200);
    }


    public function destroy($id): JsonResponse
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $todo->delete();

        return response()->json(['message' => 'Todo deleted'], 200);
    }
}
