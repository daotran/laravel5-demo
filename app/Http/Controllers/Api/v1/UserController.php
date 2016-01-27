<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('oauth');
    }

    public function getUserInfo(Request $request) {
        // return the protected resource
        //echo “success authentication”;
        $user_id = Authorizer::getResourceOwnerId(); // the token user_id
        $user = User::find($user_id); // get the user data from database

        return Response::json($user);
        //return response()->json(['data' => $user]);
    }

}
