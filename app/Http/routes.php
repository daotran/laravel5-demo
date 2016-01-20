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


/* These are RESTful API
 * Added by dao.tran 
 */

// Get all users
Route::group(['prefix' => 'api/v1'], function () {
  Route::get('users',      'Api\v1\UserController@index');
  Route::get('users/{id}', 'Api\v1\UserController@show');
});

Route::get('admin/users', function(){
    $data = Input::get('data');
});
Route::get('admin/users' , array('before' => 'ajax:data', 'as' => 'admin.users', 'uses' => 'UserController@dataRefresh'));
Route::filter('ajax', function($route, $request, $param){

    // This will give query string 'refresh'
    // if you passed it as http://domain.com?data=refresh
    $data = $request->get($param);

    // You can retrieve the $param, third argument
    // if you pass a parameter, i.e. 'refresh'
    // param will contain 'refresh'
});