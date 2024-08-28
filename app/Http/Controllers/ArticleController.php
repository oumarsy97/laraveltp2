<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponser;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //
    use ApiResponser;
    public function index()
    {
        $articles = Article::all();
        if(!$articles){
            return $this->errorResponse(null, 'No article found', 404);
        }
        return $this->successResponse($articles, 'Articles retrieved successfully', 200);
    }

    public function store(Request $request) : JsonResponse
    {
        //validation
        $validatedData = $request->validate([
            'libelle' => 'required|min:5|max:55',
            'qteStock' => 'required|numeric|gt:0',
            'prix' => 'required|numeric|gt:0',
        ]);

        // Créer un nouvel article avec les données validées
        $article = Article::create($validatedData);


        // $article = Article::create($request->all());
        return response()->json(['status' => 200, 'data' => $article, 'message' => 'Article created successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());
        return response()->json(['status' => 202, 'data' => $article, 'message' => 'Article updated successfully'], 202);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return response()->json(['status' => 202, 'data' => $article, 'message' => 'Article deleted successfully'], 202);
    }
}
