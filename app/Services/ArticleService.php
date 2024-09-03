<?php

namespace App\Services;

use App\Services\Contracts\IArticleService;
use App\Models\Article;

class ArticleService implements IArticleService
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function getAll()
    {
        return $this->article->all();
    }

    public function create(array $data)
    {
        return $this->article->create($data);
    }

    public function find(int $id)
    {
        return $this->article->find($id);
    }

    public function update(int $id, array $data)
    {
        $article = $this->find($id);
        if ($article) {
            $article->update($data);
            return $article;
        }
        return null;
    }

    public function delete(int $id)
    {
        $article = $this->find($id);
        if ($article) {
            $article->delete();
            return true;
        }
        return false;
    }

    public function findByLibelle(string $libelle)
    {
        return $this->article->where('libelle', $libelle)->first();
    }

    public function findByEtat(string $etat)
    {
        return $this->article->where('etat', $etat)->get();
    }
}
