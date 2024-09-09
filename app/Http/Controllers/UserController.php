<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function getUsers(): JsonResponse
    {
        return response()->json(User::all());
    }

}
