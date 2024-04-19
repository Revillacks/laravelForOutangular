<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all('id', 'role');
        return response()->json($roles, Response::HTTP_OK);
    }
}
