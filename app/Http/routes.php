<?php

// Home Page
Route::get('/', [
    'uses' => 'HomeController@index',
    'as' => 'home'
]);

// Choose Language
Route::get('language', 'HomeController@language');


// Admin
Route::get('admin', [
    'uses' => 'AdminController@admin',
    'as' => 'admin',
    'middleware' => 'admin'
]);

Route::get('medias', [
    'uses' => 'AdminController@filemanager',
    'as' => 'medias',
    'middleware' => 'redac'
]);

// Blog
Route::get('blog/order', ['uses' => 'BlogController@indexOrder', 'as' => 'blog.order']);
Route::get('articles', 'BlogController@indexFront');
Route::get('blog/tag', 'BlogController@tag');
Route::get('blog/search', 'BlogController@search');

Route::put('postseen/{id}', 'BlogController@updateSeen');
Route::put('postactive/{id}', 'BlogController@updateActive');

Route::resource('blog', 'BlogController');

// Comment
Route::resource('comment', 'CommentController', [
    'except' => ['create', 'show']
]);

Route::put('commentseen/{id}', 'CommentController@updateSeen');
Route::put('uservalid/{id}', 'CommentController@valid');


// Contact
Route::resource('contact', 'ContactController', [
    'except' => ['show', 'edit']
]);

// User
Route::get('user/sort/{role}', 'UserController@indexSort');

Route::get('user/roles', 'UserController@getRoles');
Route::post('user/roles', 'UserController@postRoles');

Route::put('userseen/{user}', 'UserController@updateSeen');

Route::resource('user', 'UserController');

/* ADDED BY DAO.TRAN */
/* --------------------------------
 * I. SOME ROUTES ON SITE
 * --------------------------------
*/

/* Article */
// get articles's resource
Route::resource('articles', 'ArticleController');


// Auth
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

// Paypal Testing
Route::post('payment', array(
    'as' => 'payment',
    'uses' => 'PaypalTestController@postPayment',
));

// This is after make the payment, PayPal redirect back to your site
Route::get('payment/status', array(
    'as' => 'payment.status',
    'uses' => 'PaypalTestController@getPaymentStatus',
));


/* --------------------------------
 * II. RESTFUL API CONFIGURATION
 * --------------------------------
*/

/*
 * Config Authorization Server with the Password Grant 
 */

// Generate the token after passing the username and password
// API: http://localhost/laravel5-demo/public/oauth/access_token
// config form-data/x-www-form-urlencode
// {"grant_type":"password", "client_id":"f3d259ddd3ed8ff3843839b", "client_secret":"4c7f6f8fa93d59c45502c0ae8c4a95b", "username":"testuser", "password":"password"}
Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

// Create a new user for testing
Route::get('register', function() {
    $user = new App\Models\User();
    $user->username = "noland";
    $user->email = "noland@test.com";
    $user->password = \Illuminate\Support\Facades\Hash::make("password");
    $user->role_id = 1;
    $user->save();
});

/*
 * All users's restful APIs
 */

// protected all the routes inside it with oauth (‘before’ => ‘oauth’)
Route::group(['prefix' => 'api/v1', 'before' => 'oauth'], function () {
    // get the token owner and retrieve the user info
    // API: http://localhost/laravel5-demo/public/api/v1/user_token?access_token=4dOH0eDAOTzVHDVZ8v9r97zz9CnXAmVD40rjhqzL
    Route::get('/user_token', 'Api\v1\UserController@getUserInfo');

    // get all posts of token user_id
    // API: http://localhost/laravel5-demo/public/api/v1/posts?access_token=IBzXJ9wXQAdQH1UiCjxK1A9JPIieTtEMbqerjmdF
    Route::get('/posts', 'Api\v1\PostController@getAllPosts');

    // create a new post
    // API: http://localhost/laravel5-demo/public/api/v1/posts/create
    // config form-data/x-www-form-urlencode
    // access_token=IBzXJ9wXQAdQH1UiCjxK1A9JPIieTtEMbqerjmdF
    Route::post('posts/create', 'Api\v1\PostController@create');

    // get post info of a user_id
    // config form-data/x-www-form-urlencode
    // access_token=IBzXJ9wXQAdQH1UiCjxK1A9JPIieTtEMbqerjmdF
    // API: http://localhost/laravel5-demo/public/api/v1/posts/getPostInfo?user_id=16&title=Post 9
    Route::post('posts/getPostInfo', 'Api\v1\PostController@getPostInfo');
});


Route::get('admin/users', array('before' => 'ajax:data', 'as' => 'admin.users', 'uses' => 'UserController@dataRefresh'));
Route::filter('ajax', function($route, $request, $param) {

    // This will give query string 'refresh'
    // if you passed it as http://domain.com?data=refresh&test=1
    $data = $request->get($param);

    return Response::json($data);
    // You can retrieve the $param, third argument
    // if you pass a parameter, i.e. 'refresh'
    // param will contain 'refresh'
});



/* ENDED BY DAO.TRAN */