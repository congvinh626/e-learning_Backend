<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); //bắt buộc khi sử dụng phải đăng nhập
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Task::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->can('create-tasks')) {
            $task = new Task();
            $task->content = $request->input('content');
            $task->save();
    
            return response()->json($task);
        }
    
        return response([
            'status' => false,
            'message' => 'You don\'t have permission to create task!' 
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->can('delete-tasks')) {
            $task = Task::find($id);
            $task->delete();
            return response([
                'status' => true,
            ], 200);
        }
    
        return response([
            'status' => false,
            'message' => 'You don\'t have permission to delete task!' 
        ], 200);
    }
}
