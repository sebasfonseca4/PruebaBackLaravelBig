<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            return Project::all();
        } else {
            return auth()->user()->projects;
        }
    }

    public function show(Project $project)
    {
        return response()->json($project);
    }

    public function store(Request $request)
    {   

        if ($request->has('user_id') && !User::find($request->user_id)) {
            return response()->json(['message' => 'El usuario especificado no existe.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:En proceso,Completado',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden crear proyectos.'], 403);
        }

        $project = Project::create([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'description' => $request['description'] ?? null,
            'user_id' => $request['user_id'],
        ]);
        
        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'status' => 'required|in:En proceso,Completado',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden eliminar los proyectos.'], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'Proyecto eliminado',
        ], 200);
    }
}
