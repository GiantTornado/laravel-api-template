<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;

class TaskController extends Controller {
    public function index() {
        $tasks = Task::where('user_id', auth()->id())->get();

        return TaskResource::collection($tasks);
    }
}
