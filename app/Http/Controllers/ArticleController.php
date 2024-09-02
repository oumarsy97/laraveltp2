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
    public function index(Request $request)
    {

        //verifier la quantité en stock
         $dispo = $request->query('disponible');
         if($dispo =='oui'){
            $articles = Article::where('qteStock', '>', 0)->get();
         }
         else if($dispo =='non'){
            $articles = Article::where('qteStock', '=', 0)->get();
         }else{
             $articles = Article::all();
         }
        if(!$articles){
            return $this->errorResponse(null, 'No article found', 404);
        }
        return $this->successResponse($articles, 'Articles retrieved successfully', );
    }

    public function store(StoreArticleRequest $request)
    {
        if ($request->user()->cannot('admin,boutiquier')) {
            return $this->sendResponse(null, 'Unauthorized', Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
        }
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
        if ($request->user()->cannot('admin,boutiquier')) {
            return $this->sendResponse(null, 'Unauthorized', Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
        }
        $article = Article::find($id);
        if (!$article) {
            return $this->sendResponse(null, 'Article introuvable', Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
        }

        $qteStock = $request->only('qteStock');
        if($qteStock['qteStock'] < 0){
            return $this->sendResponse(null, 'La quantité en stock ne peut pas être négative', Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
        }
        //ajouter la quantité
        $article->qteStock = $qteStock['qteStock'] + $article->qteStock;
        $article->save();
        return $this->sendResponse($article,'qte stock mis a jour', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }

    public function destroy(Request $request,$id)
{
    if ($request->user()->cannot('admin,boutiquier')) {
        return $this->sendResponse(null, 'Unauthorized', Response::HTTP_FORBIDDEN,ResponseStatus::ECHEC);
    }
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

    public function findbyLibelle(Request $request)
    {
        $libelle = $request->input('libelle');

        $articles = Article::where('libelle', 'like', '%' . $libelle . '%')->get();
        return $this->sendResponse($articles,'Article retrouve avec succès', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }

    public function updateStock(Request $request)
{
    if ($request->user()->cannot('admin,boutiquier')) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }
    // Récupérer les valeurs à partir du corps de la requête
    $articles = $request->input('articles');
    $errors = [];
    $success = [];

    foreach ($articles as $articleData) {
        $article = Article::find($articleData['id']);

        if (!$article) {
            $errors[] = $articleData['id'];
            continue;
        }

        if (!isset($articleData['qteStock'])) {
            $errors[] = $articleData['id'];
            continue;
        }

        $qteStock = $articleData['qteStock'];

        if ($qteStock < 0) {
            $errors[] = $articleData['id'];
            continue;
        }

        // Mettre à jour la quantité en stock
        $article->qteStock = $qteStock + $article->qteStock;
        $article->save();

        $success[] = $article;
    }

    return $this->sendResponse(['success' => $success, 'errors' => $errors], 'Stock mis à jour avec succès', Response::HTTP_OK, ResponseStatus::SUCCESS);
}

}
