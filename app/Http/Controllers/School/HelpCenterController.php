<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\DocumentationArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpCenterController extends Controller
{
    /**
     * Display the Help Center article index for the user's role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Map user role slug to target documentation portal string
        $portal = 'student';
        if ($user->role) {
            $slug = $user->role->slug;
            if ($slug === 'super-admin') {
                $portal = 'super-admin';
            } elseif ($slug === 'school-admin' || $slug === 'headteacher' || $slug === 'hod') {
                $portal = 'school-admin';
            } elseif ($slug === 'class-teacher' || $slug === 'subject-teacher') {
                $portal = 'teacher';
            } elseif ($slug === 'parent') {
                $portal = 'parent';
            }
        }

        $query = DocumentationArticle::where('portal', $portal)
            ->where('is_published', true)
            ->orderBy('category')
            ->orderBy('display_order');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->get()->groupBy('category');

        return view('school.help-center.index', compact('articles', 'portal'));
    }

    /**
     * Display the specified Help Center article details.
     */
    public function show($slug)
    {
        $user = Auth::user();
        $portal = 'student';
        if ($user->role) {
            $roleSlug = $user->role->slug;
            if ($roleSlug === 'super-admin') {
                $portal = 'super-admin';
            } elseif ($roleSlug === 'school-admin' || $roleSlug === 'headteacher' || $roleSlug === 'hod') {
                $portal = 'school-admin';
            } elseif ($roleSlug === 'class-teacher' || $roleSlug === 'subject-teacher') {
                $portal = 'teacher';
            } elseif ($roleSlug === 'parent') {
                $portal = 'parent';
            }
        }

        // Retrieve article corresponding to user's portal access context
        $article = DocumentationArticle::where('slug', $slug)
            ->where('portal', $portal)
            ->where('is_published', true)
            ->firstOrFail();

        // Get list of other articles in the same category/portal for navigation sidebar
        $relatedArticles = DocumentationArticle::where('portal', $portal)
            ->where('category', $article->category)
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->orderBy('display_order')
            ->get();

        return view('school.help-center.show', compact('article', 'relatedArticles'));
    }
}
