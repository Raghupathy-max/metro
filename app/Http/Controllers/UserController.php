<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pax_name' => 'required',
            'pax_email' => 'required|email',
            'pax_mobile' => 'required|min:10|max:10',
        ]);

        $user = User::create([
            'pax_name' => $request->input('pax_name'),
            'pax_email' => $request->input('pax_email'),
            'pax_mobile' => $request->input('pax_mobile'),
            'is_verified' => $request->input('is_verified'),
        ]);

        return response([
            'status' => true,
            'message' => 'user created successfully'
        ]);

    }

    public function check($pax_mobile)
    {
        $user = DB::table('users')
            ->where('pax_mobile', '=', $pax_mobile)
            ->first();

        return is_null($user) ? response([
            'status' => false,
            'error' => 'user not found'
        ]) : response([
            'status' => true,
            'user' => $user
        ]);

    }

    public function login($pax_mobile)
    {
        $user = User::where('pax_mobile', '=', $pax_mobile)->first();

        return response([
            'status' => true,
            'message' => 'user login successfully',
            'user'=> $user
        ]);

    }

}
