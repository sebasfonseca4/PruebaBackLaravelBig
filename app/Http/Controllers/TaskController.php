<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tasks = Task::with('project', 'assignedUser')->get();
        return response()->json($tasks);
    }

    public function show($id)
    {
        $task = Task::with('project', 'assignedUser')->findOrFail($id);
        return response()->json($task);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Pendiente,En progreso,Completado',
            'due_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($request->has('assigned_to') && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden asignar tareas a otros usuarios.'], 403);
        }

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:Pendiente,En progreso,Completado',
            'due_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = Task::findOrFail($id);

        if ($request->has('assigned_to') && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden asignar tareas a otros usuarios.'], 403);
        }

        $task->update($validated);

        return response()->json($task);
    }


    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Solo los administradores pueden eliminar tareas.'], 403);
        }

        $task = Task::findOrFail($id);

        $task->delete();
        
        return response()->json(['message' => 'Tarea eliminada correctamente.']);
    }
}
