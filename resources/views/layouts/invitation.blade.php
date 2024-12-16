<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $invitation->title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    @if($preview)
        <div class="fixed top-0 left-0 right-0 bg-purple-600 text-white text-center py-2 z-50">
            תצוגה מקדימה
        </div>
    @endif

    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @if($preview)
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 flex justify-center gap-4">
            <x-button>שמור טיוטה</x-button>
            <x-button variant="primary">פרסם הזמנה</x-button>
        </div>
    @endif

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
</body>
</html>