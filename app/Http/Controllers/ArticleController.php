<?php


namespace App\Http\Controllers;

use App\Services\Contracts\IArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(IArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {
      return   $this->articleService->getAll();

    }

    public function store(Request $request)
    {
        $article = $this->articleService->create($request->all());
        return $article;
    }

    public function show($id)
    {
        $article = $this->articleService->find($id);
        if ($article) {
            return $article;
        }
        return response()->json(['message' => 'Article not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $article = $this->articleService->update($id, $request->all());
        if ($article) {
            return $article;
        }
        return response()->json(['message' => 'Article not found'], 404);
    }

    public function destroy($id)
    {
        if ($this->articleService->delete($id)) {
            return response()->json(['message' => 'Article deleted'], 200);
        }
        return response()->json(['message' => 'Article not found'], 404);
    }
}
