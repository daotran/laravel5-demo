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

    /**
     * Store a newly created post in storage.
     *
     * @return Response
     */
    public function create() {
        $status = false;

        $post = new Post;
        $user_id = Authorizer::getResourceOwnerId(); // the token user_id

        $post->title = "Post 512a";
        $post->slug = "post-512a";
        $post->content = "Content of Post 5";
        $post->seen = 0;
        $post->active = 1;
        $post->user_id = $user_id;

        // $post->title = Request::get('url');
        //$url->description = Request::get('description');
        // Validation and Filtering is sorely needed!!
        // Seriously, I'm a bad person for leaving that out.
        if ($post->save()) {
            $status = true;
        }

        if ($status) {
            return Response::json(array(
                        'success' => TRUE,
                        'data' => $post->toArray()), 200
            );
        } else {
            return Response::json(array(
                        'success' => FALSE,
                        'message' => 'Sorry, create new post unsuccessfully!'
            ));
        }
    }

}
