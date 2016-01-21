<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('oauth');
    }

    /**
     * Get all posts of the token owner user info
     *
     * @return Posts
     */
    public function getAllPosts(Request $request) {

        $user_id = Authorizer::getResourceOwnerId(); // the token user_id
        $user = User::find($user_id); // get the user data from database
        
        // get all posts
        //$posts = $user->posts()->get(); //OR as below
        $posts = Post::where('user_id', '=', $user->id)->get();

        return Response::json($posts);
    }

}
