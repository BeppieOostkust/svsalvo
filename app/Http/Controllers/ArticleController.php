<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\PublicStorage;
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
            ->paginate(12)
            ->through(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));

        $featuredArticles = Article::with(['author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));

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
        PublicStorage::expose($article, 'featured_image');

        $relatedArticles = Article::with(['author'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));

        return Inertia::render('ArticleDetail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
