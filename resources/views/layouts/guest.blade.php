<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ seo_description() }}">
    <meta name="keywords" content="{{ seo_keywords() }}">
    
    <title>@yield('title', seo_title())</title>
    
    <link rel="canonical" href="{{ seo_canonical_url() }}">
    <link rel="icon" href="{{ app_favicon() }}">
    
    <meta property="og:title" content="@yield('og_title', seo_og_title())">
    <meta property="og:description" content="@yield('og_description', seo_og_description())">
    <meta property="og:image" content="@yield('og_image', seo_og_image())">
    <meta property="og:url" content="{{ seo_canonical_url() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ app_name() }}">
    
    <meta name="twitter:card" content="{{ seo_twitter_card() }}">
    <meta name="twitter:title" content="@yield('og_title', seo_og_title())">
    <meta name="twitter:description" content="@yield('og_description', seo_og_description())">
    <meta name="twitter:image" content="@yield('og_image', seo_og_image())">
    
    @stack('preload')
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="application/ld+json">
        {!! seo_json_ld() !!}
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary-color: {{ primary_color() }};
            --secondary-color: {{ system_setting('secondary_color', '#10B981') }};
        }
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    @yield('content')
    <x-ai-chat-widget mode="sales" />
    @stack('scripts')
</body>
</html>
