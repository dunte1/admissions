<?php

use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $schools = \App\Models\School::where('status', 'active')->get();
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Static pages
    $staticPages = [
        ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
        ['url' => route('login'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => route('register'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => route('super-admin.login'), 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];
    
    foreach ($staticPages as $page) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . htmlspecialchars($page['url']) . '</loc>';
        $sitemap .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
        $sitemap .= '<priority>' . $page['priority'] . '</priority>';
        $sitemap .= '</url>';
    }
    
    // School landing pages
    foreach ($schools as $school) {
        $schoolUrl = $school->domain 
            ? 'https://' . $school->domain 
            : route('home');
        
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . htmlspecialchars($schoolUrl) . '</loc>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap, 200, [
        'Content-Type' => 'application/xml',
        'charset' => 'utf-8',
    ]);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $sitemapUrl = route('sitemap');
    
    $robots = "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /student/\n";
    $robots .= "Disallow: /super-admin/\n";
    $robots .= "Disallow: /api/\n";
    $robots .= "Sitemap: {$sitemapUrl}\n";
    
    return response($robots, 200, [
        'Content-Type' => 'text/plain',
    ]);
})->name('robots');