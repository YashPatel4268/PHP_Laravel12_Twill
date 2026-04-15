<?php

namespace App\Http\Controllers;

use A17\Twill\Facades\TwillAppSettings;
use App\Repositories\PageRepository;
use Illuminate\Contracts\View\View;
use App\Models\Page;

class PageDisplayController extends Controller
{
    public function show(string $slug, PageRepository $pageRepository): View
    {
        $page = $pageRepository->forSlug($slug);

        if (!$page) {
            abort(404);
        }

        // ✅ 1. Increment Page Views
        $page->increment('views');

        // ✅ 2. Get Related Pages
        $relatedPages = Page::where('id', '!=', $page->id)
            ->where('published', 1)
            ->latest()
            ->take(3)
            ->get();

        return view('site.page', [
            'item' => $page,
            'relatedPages' => $relatedPages
        ]);
    }

    public function home(): View
    {
        if (TwillAppSettings::get('homepage.homepage.page')->isNotEmpty()) {

            /** @var \App\Models\Page $frontPage */
            $frontPage = TwillAppSettings::get('homepage.homepage.page')->first();

            if ($frontPage->published) {

                // ✅ Increment views for homepage
                $frontPage->increment('views');

                return view('site.page', [
                    'item' => $frontPage
                ]);
            }
        }

        abort(404);
    }
}