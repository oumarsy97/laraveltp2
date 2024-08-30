<?php

namespace App\Http\Controllers;

use App\Enums\EnumRole;
use App\Enums\ResponseStatus;
use App\Traits\ApiResponser;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return $this->successResponse($articles, 'Articles retrieved successfully', );
    }

    public function store(StoreArticleRequest $request)
    {
        try{
        //validation
        $validatedData = $request->validated();

        // Créer un nouvel article avec les données validées
        $article = Article::create($validatedData);
        // dd($article);

        return $this->sendResponse($article,'Article créé avec succès', Response::HTTP_CREATED,ResponseStatus::SUCCESS);
        } catch (\Exception $e) {
            return $this->sendResponse(null, $e->getMessage(), Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
        }
      }

    public function update(Request $request, $id)
     {
    //     $article = Article::findOrFail($id);
    //     $article->update($request->all());
    //     return response()->json(['status' => 202, 'data' => $article, 'message' => 'Article updated successfully'], 202);
    }

    public function destroy($id)
{
    $article = Article::findOrFail($id);
    $article->delete();

    return $this->sendResponse($article,'Article supprimé avec succès', Response::HTTP_OK,ResponseStatus::SUCCESS);
}



    public function show($id)
    {
        try{
        $article = Article::find($id);
        if(!$article){
            return $this->sendResponse(null, 'Article introuvable', Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
        }

        return $this->sendResponse($article,'Article retrouve avec succès', Response::HTTP_OK,ResponseStatus::SUCCESS);
        }catch(ValidationException $e){
            return $this->sendResponse(null, $e->getMessage(), Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
        }
    }

    public function updateStock(Request $request)
    {

        // Récupérer les valeurs à partir du corps de la requête
        $articles = $request->input('articles');
        $error = [];
        foreach ($articles as $article) {
            $article = Article::find($article['id']);
            $article->update([
                'qte' => $article['qte'],
            ]);
        }
        return $this->sendResponse(null, 'Stock mis à jour avec succès', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }
}
