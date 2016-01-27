<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
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

    /**
     * Store a newly created post in storage.
     *
     * @return Response
     */
    public function create() {

        $result = array();
        $status = false;
        
        $user_id = Authorizer::getResourceOwnerId(); // the token user_id
        
        // get json string from the Request Body in a POST request
        $input = Input::all(); // OR using $t = \Illuminate\Support\Facades\Request::all(); return $t;
        
        // store post record
        $post = new Post;
        $post->title = $input['title'];
        $post->slug = $input['slug'];
        $post->content = $input['content'];
        $post->seen = 0;
        $post->active = 1;
        $post->user_id = $user_id;

        if ($post->save()) {
            $status = true;
        }

        if ($status) {
            Response::json(array(
                $result['success'] = TRUE,
                $result['data'] = $post->toArray()), 200 // insert data successfully
            );
        } else {
            Response::json(array(
                $result['success'] = FALSE,
                $result['message'] = 'Sorry, new post is created unsuccessfully!'), 409 // a duplicate data in the database
            );
        }

        return $result;
    }

    public function getPostInfo() {

        $filters = Input::only('user_id', 'title');
        //$user_id = Input::get('user_id'); 
        //$title = Input::get('title');

        $posts = Post::where($filters)->get();
        return $posts;
    }

}
