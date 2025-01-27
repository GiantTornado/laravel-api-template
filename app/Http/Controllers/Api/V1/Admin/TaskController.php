<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;

class TaskController extends Controller {
    public function index() {
        $tasks = Task::with(['user'])->get();

        return TaskResource::collection($tasks);
    }
}
