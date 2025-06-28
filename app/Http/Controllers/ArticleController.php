<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles
     */
    public function index()
    {
        $articles = Article::with(['author'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $featuredArticles = Article::with(['author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('Nieuws', [
            'articles' => $articles,
            'featuredArticles' => $featuredArticles,
        ]);
    }

    /**
     * Display a single article
     */
    public function show($slug)
    {
        $article = Article::with(['author', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $relatedArticles = Article::with(['author'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('ArticleDetail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
