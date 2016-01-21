<?php

// Home
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


// Auth
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


/* RESTFUL API CONFIGURATION
 * Added by dao.tran 
 */

/* 
 * Config Authorization Server with the Password Grant 
 */

// Generate the token after passing the username and password
Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

// Create a new user for testing
Route::get('register',function(){
    $user = new App\Models\User();
    $user->username = "test user";
    $user->email = "test@test.com";
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
    Route::get('/user_token', 'Api\v1\UserController@getUserInfo');
    
    // get all posts of token user_id
    Route::get('/posts',  'Api\v1\PostController@getAllPosts');
    
});


//
//Route::get('admin/users', function(){
//    $data = Input::get('data');
//});
//Route::get('admin/users' , array('before' => 'ajax:data', 'as' => 'admin.users', 'uses' => 'UserController@dataRefresh'));
//Route::filter('ajax', function($route, $request, $param){
//
//    // This will give query string 'refresh'
//    // if you passed it as http://domain.com?data=refresh
//    $data = $request->get($param);
//
//    // You can retrieve the $param, third argument
//    // if you pass a parameter, i.e. 'refresh'
//    // param will contain 'refresh'
//});


//Route::get('oauth/authorize', ['as' => 'oauth.authorize.get', 'middleware' => ['check-authorization-params', 'auth'], function() {
//   $authParams = \LucaDegasperi\OAuth2Server\Authorizer::getAuthCodeRequestParams();
//
//   $formParams = array_except($authParams,'client');
//
//   $formParams['client_id'] = $authParams['client']->getId();
//
//   $formParams['scope'] = implode(config('oauth2.scope_delimiter'), array_map(function ($scope) {
//       return $scope->getId();
//   }, $authParams['scopes']));
//
//   return \Illuminate\View\View::make('oauth.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
//}]);



/* Ended by dao.tran */