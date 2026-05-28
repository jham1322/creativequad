@php
    $seoTitle = $title ?? 'Creative Quad Vibe Coding Course Tagalog | Build Web Apps Using AI and Codex';
    $seoDescription = $description ?? 'Learn how to build real web apps using AI and Codex in a step-by-step Tagalog course from Creative Quad.';
    $seoRobots = $robots ?? 'index,follow';
    $seoCanonical = $canonical ?? url()->current();
    $seoImagePath = 'images/hero/social-preview.jpg';
    $seoImageVersion = @filemtime(public_path($seoImagePath)) ?: time();
    $seoImage = $image ?? asset($seoImagePath) . '?v=' . $seoImageVersion;
    $faviconVersion = @filemtime(public_path('favicon.png')) ?: time();
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v={{ $faviconVersion }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:secure_url" content="{{ $seoImage }}">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Creative Quad Vibe Coding Course preview">
<meta property="og:site_name" content="Creative Quad">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
