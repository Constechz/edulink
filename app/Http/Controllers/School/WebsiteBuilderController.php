<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use App\Models\PageRevision;
use App\Models\WebsiteBlock;
use App\Models\WebsiteSettings;
use App\Models\WebsiteMenu;
use App\Models\WebsiteMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebsiteBuilderController extends Controller
{
    /**
     * List all pages catalog.
     */
    public function pagesIndex(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $pages = WebsitePage::where('school_id', $schoolId)->orderBy('display_order')->get();

        return view('school.website.pages', compact('pages'));
    }

    /**
     * Store new page config.
     */
    public function pagesStore(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'page_type' => 'required|in:home,about,admissions,contact,news,events,gallery,custom',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->slug);

        // Enforce unique slug per school
        if (WebsitePage::where('school_id', $schoolId)->where('slug', $slug)->exists()) {
            return redirect()->back()->withInput()->withErrors(['slug' => 'The page slug is already in use.']);
        }

        try {
            DB::transaction(function () use ($request, $schoolId, $slug) {
                // If it is the first page, mark it as homepage
                $isHomepage = !WebsitePage::where('school_id', $schoolId)->exists();
                if ($isHomepage) {
                    $slug = 'home';
                }

                $page = WebsitePage::create([
                    'school_id' => $schoolId,
                    'title' => $request->title,
                    'slug' => $slug,
                    'meta_description' => $request->meta_description,
                    'page_type' => $request->page_type,
                    'is_published' => false,
                    'is_homepage' => $isHomepage,
                    'display_order' => WebsitePage::where('school_id', $schoolId)->count() + 1,
                    'created_by' => $request->user()->id,
                ]);

                // Create initial blank revision
                PageRevision::create([
                    'website_page_id' => $page->id,
                    'revision_number' => 1,
                    'html_content' => '<div class="py-5 text-center"><p class="lead">Drag and drop blocks to start editing your ' . e($page->title) . ' page.</p></div>',
                    'css_content' => '',
                    'components_json' => '[]',
                    'is_current_draft' => true,
                    'is_published' => false,
                    'created_by' => $request->user()->id,
                ]);
            });

            return redirect()->back()->with('success', 'Website page initialized successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to initialize page: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete page (soft-deletes).
     */
    public function pagesDestroy(Request $request, WebsitePage $page)
    {
        $schoolId = $request->user()->school_id;

        if ($page->school_id !== $schoolId) {
            abort(403);
        }

        if ($page->is_homepage) {
            return redirect()->back()->withErrors(['error' => 'The homepage cannot be deleted.']);
        }

        $page->delete();

        return redirect()->back()->with('success', 'Page removed successfully.');
    }

    /**
     * Open page in GrapesJS editor.
     */
    public function builderEdit(Request $request, WebsitePage $page)
    {
        $schoolId = $request->user()->school_id;

        if ($page->school_id !== $schoolId) {
            abort(403);
        }

        $revision = PageRevision::where('website_page_id', $page->id)
            ->where('is_current_draft', true)
            ->latest()
            ->first();

        if (!$revision) {
            // Fallback: create draft revision from the latest published one or blank
            $latest = PageRevision::where('website_page_id', $page->id)->latest()->first();
            $num = $latest ? $latest->revision_number + 1 : 1;

            $revision = PageRevision::create([
                'website_page_id' => $page->id,
                'revision_number' => $num,
                'html_content' => $latest ? $latest->html_content : '',
                'css_content' => $latest ? $latest->css_content : '',
                'components_json' => $latest ? $latest->components_json : '[]',
                'is_current_draft' => true,
                'is_published' => false,
                'created_by' => $request->user()->id,
            ]);
        }

        $blocks = WebsiteBlock::where('is_active', true)->orderBy('display_order')->get();
        $settings = WebsiteSettings::firstOrCreate(['school_id' => $schoolId], [
            'site_name' => $request->user()->school->name ?? config('app.name', 'EduLink') . ' School',
            'is_published' => false,
        ]);

        return view('school.website.builder', compact('page', 'revision', 'blocks', 'settings'));
    }

    /**
     * AJAX Endpoint: Save draft revision from GrapesJS.
     */
    public function builderSave(Request $request, WebsitePage $page)
    {
        $schoolId = $request->user()->school_id;

        if ($page->school_id !== $schoolId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'html' => 'required|string',
            'css' => 'nullable|string',
            'components' => 'required|string', // JSON string from GrapesJS
        ]);

        try {
            DB::transaction(function () use ($request, $page) {
                // Find or create current draft revision
                $revision = PageRevision::where('website_page_id', $page->id)
                    ->where('is_current_draft', true)
                    ->first();

                // If no active draft exists, or if the latest revision is published, create a new revision number
                if (!$revision) {
                    $latest = PageRevision::where('website_page_id', $page->id)->orderBy('revision_number', 'desc')->first();
                    $num = $latest ? $latest->revision_number + 1 : 1;

                    $revision = new PageRevision();
                    $revision->website_page_id = $page->id;
                    $revision->revision_number = $num;
                    $revision->is_current_draft = true;
                    $revision->is_published = false;
                }

                $revision->html_content = $request->html;
                $revision->css_content = $request->css ?? '';
                $revision->components_json = $request->components;
                $revision->created_by = $request->user()->id;
                $revision->save();

                // Keep only the last 10 revisions to avoid database bloating
                $oldRevisions = PageRevision::where('website_page_id', $page->id)
                    ->orderBy('created_at', 'desc')
                    ->skip(10)
                    ->take(100)
                    ->get();

                foreach ($oldRevisions as $old) {
                    if (!$old->is_published && !$old->is_current_draft) {
                        $old->delete();
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Draft saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Publish the page draft.
     */
    public function builderPublish(Request $request, WebsitePage $page)
    {
        $schoolId = $request->user()->school_id;

        if ($page->school_id !== $schoolId) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $page) {
                $draft = PageRevision::where('website_page_id', $page->id)
                    ->where('is_current_draft', true)
                    ->firstOrFail();

                // Update all previous published revisions to false
                PageRevision::where('website_page_id', $page->id)
                    ->where('is_published', true)
                    ->update(['is_published' => false]);

                // Mark current draft as published
                $draft->update([
                    'is_published' => true,
                    'is_current_draft' => false, // No longer just a draft, it is now the live version!
                    'published_at' => now(),
                    'published_by' => $request->user()->id,
                ]);

                // Update page status
                $page->update([
                    'is_published' => true,
                    'published_at' => now(),
                    'updated_by' => $request->user()->id,
                ]);

                // Auto create a fresh draft from this published version for future edits
                PageRevision::create([
                    'website_page_id' => $page->id,
                    'revision_number' => $draft->revision_number + 1,
                    'html_content' => $draft->html_content,
                    'css_content' => $draft->css_content,
                    'components_json' => $draft->components_json,
                    'is_current_draft' => true,
                    'is_published' => false,
                    'created_by' => $request->user()->id,
                ]);
            });

            return redirect()->back()->with('success', 'Page published live successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to publish page: ' . $e->getMessage()]);
        }
    }

    /**
     * View revision log.
     */
    public function builderRevisions(Request $request, WebsitePage $page)
    {
        $schoolId = $request->user()->school_id;

        if ($page->school_id !== $schoolId) {
            abort(403);
        }

        $revisions = PageRevision::where('website_page_id', $page->id)
            ->with(['publisher', 'creator'])
            ->orderBy('revision_number', 'desc')
            ->get();

        return view('school.website.revisions', compact('page', 'revisions'));
    }

    /**
     * Rollback to a previous revision.
     */
    public function builderRollback(Request $request, PageRevision $revision)
    {
        $schoolId = $request->user()->school_id;
        $page = $revision->websitePage;

        if ($page->school_id !== $schoolId) {
            abort(403);
        }

        try {
            DB::transaction(function () use ($request, $page, $revision) {
                // Get latest revision number before deleting
                $latest = PageRevision::where('website_page_id', $page->id)->orderBy('revision_number', 'desc')->first();
                $num = $latest ? $latest->revision_number + 1 : 1;

                // Delete any current draft
                PageRevision::where('website_page_id', $page->id)
                    ->where('is_current_draft', true)
                    ->delete();

                // Create new draft copy from the chosen rollback revision
                PageRevision::create([
                    'website_page_id' => $page->id,
                    'revision_number' => $num,
                    'html_content' => $revision->html_content,
                    'css_content' => $revision->css_content,
                    'components_json' => $revision->components_json,
                    'is_current_draft' => true,
                    'is_published' => false,
                    'notes' => 'Rolled back to revision #' . $revision->revision_number,
                    'created_by' => $request->user()->id,
                ]);
            });

            return redirect()->route('school.website.pages.builder', $page->id)->with('success', 'Page rolled back to revision #' . $revision->revision_number . ' draft status.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to rollback revision: ' . $e->getMessage()]);
        }
    }

    /**
     * Show site branding and settings edit page.
     */
    public function settingsEdit(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $settings = WebsiteSettings::firstOrCreate(['school_id' => $schoolId], [
            'site_name' => $request->user()->school->name ?? config('app.name', 'EduLink') . ' School',
            'is_published' => false,
        ]);

        return view('school.website.settings', compact('settings'));
    }

    /**
     * Save branding and theme parameters.
     */
    public function settingsUpdate(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $settings = WebsiteSettings::where('school_id', $schoolId)->firstOrFail();

        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'primary_color' => 'required|string|max:10',
            'secondary_color' => 'required|string|max:10',
            'accent_color' => 'required|string|max:10',
            'text_color' => 'required|string|max:10',
            'bg_color' => 'required|string|max:10',
            'heading_font' => 'required|string',
            'body_font' => 'required|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
            'contact_map_embed' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'is_published' => 'nullable|boolean',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
        ]);

        try {
            $data = $request->except(['logo', 'favicon']);
            $data['is_published'] = $request->has('is_published');

            // Handle uploads
            if ($request->hasFile('logo')) {
                $data['logo_path'] = $request->file('logo')->store('website/logos', 'public');
            }
            if ($request->hasFile('favicon')) {
                $data['favicon_path'] = $request->file('favicon')->store('website/favicons', 'public');
            }

            $settings->update($data);

            return redirect()->back()->with('success', 'Branding settings saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to save settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Show navigation builder.
     */
    public function navigationEdit(Request $request)
    {
        $schoolId = $request->user()->school_id;

        // Ensure header and footer menus exist
        $headerMenu = WebsiteMenu::firstOrCreate(['school_id' => $schoolId, 'location' => 'header'], ['name' => 'Main Navigation']);
        $footerMenu = WebsiteMenu::firstOrCreate(['school_id' => $schoolId, 'location' => 'footer'], ['name' => 'Footer Links']);

        $headerItems = WebsiteMenuItem::where('menu_id', $headerMenu->id)
            ->with('page')
            ->orderBy('display_order')
            ->get();

        $footerItems = WebsiteMenuItem::where('menu_id', $footerMenu->id)
            ->with('page')
            ->orderBy('display_order')
            ->get();

        $pages = WebsitePage::where('school_id', $schoolId)->get();

        return view('school.website.navigation', compact('headerItems', 'footerItems', 'pages', 'headerMenu', 'footerMenu'));
    }

    /**
     * Save navigation items tree.
     */
    public function navigationUpdate(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $request->validate([
            'menu_id' => 'required|exists:website_menus,id',
            'items' => 'required|array',
            'items.*.label' => 'required|string|max:100',
            'items.*.url' => 'nullable|string|max:255',
            'items.*.page_id' => 'nullable|exists:website_pages,id',
            'items.*.open_new_tab' => 'nullable|boolean',
        ]);

        $menu = WebsiteMenu::where('id', $request->menu_id)->where('school_id', $schoolId)->firstOrFail();

        try {
            DB::transaction(function () use ($request, $menu) {
                // Drop existing items and recreate
                WebsiteMenuItem::where('menu_id', $menu->id)->delete();

                foreach ($request->items as $index => $itemData) {
                    if (empty($itemData['label'])) {
                        continue;
                    }
                    WebsiteMenuItem::create([
                        'menu_id' => $menu->id,
                        'label' => $itemData['label'],
                        'url' => $itemData['url'] ?? null,
                        'page_id' => $itemData['page_id'] ?? null,
                        'open_new_tab' => isset($itemData['open_new_tab']) ? (bool) $itemData['open_new_tab'] : false,
                        'display_order' => $index,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Navigation menu updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to save navigation: ' . $e->getMessage()]);
        }
    }
}
