<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationArticle;
use Illuminate\Http\Request;

class SuperAdminDocumentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DocumentationArticle::orderBy('display_order', 'asc')->orderBy('created_at', 'desc');

        if ($request->filled('portal')) {
            $query->where('portal', $request->portal);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->paginate(15);

        return view('super-admin.documentation.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('super-admin.documentation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'portal' => 'required|string|in:super-admin,school-admin,teacher,student,parent',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        DocumentationArticle::create([
            'portal' => $request->portal,
            'category' => $request->category,
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
            'display_order' => $request->display_order,
        ]);

        return redirect()->route('super-admin.documentation.index')->with('success', 'Documentation article created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $article = DocumentationArticle::findOrFail($id);
        return view('super-admin.documentation.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $article = DocumentationArticle::findOrFail($id);

        $request->validate([
            'portal' => 'required|string|in:super-admin,school-admin,teacher,student,parent',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'nullable|boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        $article->update([
            'portal' => $request->portal,
            'category' => $request->category,
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
            'display_order' => $request->display_order,
        ]);

        return redirect()->route('super-admin.documentation.index')->with('success', 'Documentation article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $article = DocumentationArticle::findOrFail($id);
        $article->delete();

        return redirect()->route('super-admin.documentation.index')->with('success', 'Documentation article deleted successfully.');
    }
}
