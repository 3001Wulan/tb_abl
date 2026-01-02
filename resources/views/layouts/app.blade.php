<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem KP')</title>
    
   <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .header-right { width: calc(100% - 3.5rem); }
        .sidebar:hover { width: 16rem; }
        @media only screen and (min-width: 768px) {
            .header-right { width: calc(100% - 16rem); }         
        }
    </style>
    
    @stack('styles')
    {{-- SweetAlert2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body x-data="{ isDark: localStorage.getItem('dark') === 'true' }" 
      :class="{ 'dark': isDark }"
      x-init="$watch('isDark', val => localStorage.setItem('dark', val))">
      
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-white dark:bg-gray-700 text-black dark:text-white transition-colors duration-200">
        
        @include('partials.header') 
        @include('partials.sidebar')

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
    {{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>