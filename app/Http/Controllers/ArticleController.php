<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function redirect;
use function view;

class ArticleController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // get all articles
        $articles = Article::all();

        return view('articles.index')->with('articles', $articles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ArticleRequest $request) {
        $input_data = $request->all();
        /*
          $article = new Article();
          $article->name = $input_data['name'];
          $article->author = $input_data['author'];
          $article->save(); */

        Article::create($input_data);

        return redirect('articles'); // show all artilces after creating
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        // find the article object you want to edit
        $article = Article::findOrNew($id);

        // Invoke edit page to show the article
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ArticleRequest $request, $id) {

        // retrieve article object
        $article = Article::findOrNew($id);

        // update article information that getting from form input
        $article->update($request->all());

        return redirect('articles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
