<?php

namespace App\Http\Controllers;

use App\Models\WebsitePage;
use App\Models\PageRevision;
use App\Models\WebsiteSettings;
use App\Models\WebsiteMenu;
use App\Models\WebsiteMenuItem;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Staff;
use App\Models\WebsiteGalleryItem;
use App\Models\School;
use Illuminate\Http\Request;

class PublicWebsiteController extends Controller
{
    /**
     * Show the public tenant page based on slug or homepage.
     */
    public function showPage(Request $request, $slug = null)
    {
        $school = $this->getTenantSchool($request);

        if (!$school) {
            abort(404, 'School tenant not found.');
        }

        // Check if Custom Website Builder is unlocked
        $settings = $school->settings ?: [];
        $unlocked = isset($settings['website_builder_unlocked']) && $settings['website_builder_unlocked'] == true;

        if (!$unlocked) {
            return response()->view('school.website.offline_placeholder', compact('school'));
        }

        // Look up the requested page
        if (empty($slug) || $slug === 'home') {
            $page = WebsitePage::where('school_id', $school->id)
                ->where('is_homepage', true)
                ->first();
        } else {
            $page = WebsitePage::where('school_id', $school->id)
                ->where('slug', $slug)
                ->first();
        }

        if (!$page) {
            abort(404, 'Page not found.');
        }

        // Fetch published page revision
        $revision = PageRevision::where('website_page_id', $page->id)
            ->where('is_published', true)
            ->first();

        // If no published revision exists, check if user is logged in as admin of this school and show draft
        if (!$revision) {
            if (auth()->check() && auth()->user()->school_id === $school->id) {
                $revision = PageRevision::where('website_page_id', $page->id)
                    ->where('is_current_draft', true)
                    ->first();
            }
        }

        if (!$revision) {
            abort(404, 'Page content is not published yet.');
        }

        // Load website settings/branding
        $settings = WebsiteSettings::firstOrCreate(['school_id' => $school->id], [
            'site_name' => $school->name,
            'is_published' => false,
        ]);

        // Load header/footer menus
        $headerMenu = WebsiteMenu::where('school_id', $school->id)->where('location', 'header')->first();
        $headerItems = $headerMenu 
            ? WebsiteMenuItem::where('menu_id', $headerMenu->id)->with('page')->orderBy('display_order')->get() 
            : collect();

        $footerMenu = WebsiteMenu::where('school_id', $school->id)->where('location', 'footer')->first();
        $footerItems = $footerMenu 
            ? WebsiteMenuItem::where('menu_id', $footerMenu->id)->with('page')->orderBy('display_order')->get() 
            : collect();

        // Dynamically rewrite hardcoded absolute paths inside database HTML content for subfolder setups
        if ($revision && !empty($revision->html_content)) {
            $applyUrl = route('school.admissions.apply') . '?school_id=' . $school->id;
            $html = $revision->html_content;
            
            $html = str_replace([
                'href="/admissions/apply"',
                'href="/admissions/apply?school_id=' . $school->id . '"',
            ], 'href="' . $applyUrl . '"', $html);

            $html = str_replace(
                'href="/public-site/',
                'href="' . url('/public-site/') . '/',
                $html
            );

            $revision->html_content = $html;
        }

        return view('school.website.public_theme', compact('school', 'page', 'revision', 'settings', 'headerItems', 'footerItems'));
    }

    /**
     * Submit Contact Form.
     */
    public function contactSubmit(Request $request)
    {
        $school = $this->getTenantSchool($request);

        if (!$school) {
            return response()->json(['error' => 'Tenant school not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // In a complete implementation, this might create a support ticket, dispatch an email,
        // or write to a dynamic contact inquiries table. For now, we return a successful response.
        return redirect()->back()->with('success', 'Thank you for reaching out! We have received your inquiry.');
    }

    /**
     * JSON Endpoint: Latest news feed.
     */
    public function newsFeed(Request $request)
    {
        $school = $this->getTenantSchool($request);
        if (!$school) return response()->json([], 404);

        $limit = $request->get('limit', 6);
        $news = Announcement::where('school_id', $school->id)
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($news);
    }

    /**
     * JSON Endpoint: Dynamic upcoming events.
     */
    public function eventsFeed(Request $request)
    {
        $school = $this->getTenantSchool($request);
        if (!$school) return response()->json([], 404);

        $limit = $request->get('limit', 5);
        $events = Event::where('school_id', $school->id)
            ->orderBy('start_time')
            ->limit($limit)
            ->get();

        return response()->json($events);
    }

    /**
     * JSON Endpoint: Featured staff catalog.
     */
    public function staffFeed(Request $request)
    {
        $school = $this->getTenantSchool($request);
        if (!$school) return response()->json([], 404);

        $limit = $request->get('limit', 6);
        $staff = Staff::where('school_id', $school->id)
            ->with('user')
            ->limit($limit)
            ->get();

        return response()->json($staff);
    }

    /**
     * JSON Endpoint: Photo gallery log.
     */
    public function galleryFeed(Request $request)
    {
        $school = $this->getTenantSchool($request);
        if (!$school) return response()->json([], 404);

        $limit = $request->get('limit', 12);
        $items = WebsiteGalleryItem::where('school_id', $school->id)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->limit($limit)
            ->get();

        return response()->json($items);
    }

    /**
     * Dynamic compile of theme parameters into CSS variables.
     */
    public function brandingCss(Request $request, $schoolId)
    {
        $settings = WebsiteSettings::where('school_id', $schoolId)->first();
        
        $primary = $settings->primary_color ?? '#003366';
        $secondary = $settings->secondary_color ?? '#FFD700';
        $accent = $settings->accent_color ?? '#FF6B35';
        $text = $settings->text_color ?? '#333333';
        $bg = $settings->bg_color ?? '#FFFFFF';
        $hFont = $settings->heading_font ?? 'Outfit';
        $bFont = $settings->body_font ?? 'Inter';

        $css = "
        :root {
            --primary-color: {$primary};
            --secondary-color: {$secondary};
            --accent-color: {$accent};
            --text-color: {$text};
            --bg-color: {$bg};
            --heading-font: '{$hFont}', sans-serif;
            --body-font: '{$bFont}', sans-serif;
        }
        ";

        return response($css)->header('Content-Type', 'text/css');
    }

    /**
     * Show page using path-based subdomain parameter.
     */
    public function showPageByPath(Request $request, $school_subdomain, $slug = null)
    {
        $school = School::where('subdomain', $school_subdomain)->first();
        if (!$school) {
            abort(404, 'School not found.');
        }

        app()->instance('tenant', $school);
        $request->session()->put('school_id', $school->id);

        return $this->showPage($request, $slug ?: 'home');
    }

    /**
     * Submit contact form using path-based subdomain parameter.
     */
    public function contactSubmitByPath(Request $request, $school_subdomain)
    {
        $school = School::where('subdomain', $school_subdomain)->first();
        if (!$school) {
            abort(404, 'School not found.');
        }

        app()->instance('tenant', $school);
        $request->session()->put('school_id', $school->id);

        return $this->contactSubmit($request);
    }

    /**
     * Helper to resolve the active tenant school model.
     */
    private function getTenantSchool(Request $request)
    {
        if (app()->bound('tenant')) {
            return app('tenant');
        }

        // Path-based identification fallback
        $subdomainParam = $request->route() ? $request->route()->parameter('school_subdomain') : null;
        if ($subdomainParam) {
            $school = School::where('subdomain', $subdomainParam)->first();
            if ($school) {
                app()->instance('tenant', $school);
                return $school;
            }
        }

        // Fallback for localhost testing
        $schoolId = $request->header('X-School-ID') 
                    ?? $request->get('school_id') 
                    ?? $request->session()->get('school_id');

        if ($schoolId) {
            $school = School::find($schoolId);
            if ($school) {
                app()->instance('tenant', $school);
                return $school;
            }
        }

        // Return first school as general fallback
        $school = School::first();
        if ($school) {
            app()->instance('tenant', $school);
            return $school;
        }

        return null;
    }
}
