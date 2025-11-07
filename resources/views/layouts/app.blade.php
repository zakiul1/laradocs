<!doctype html>
<html lang="en" x-data class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Siatex Docs — Admin')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('layout', {
                sidebarOpen: true,
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen
                }
            })
        })
    </script>

    <style>
        .scroll-thin::-webkit-scrollbar {
            width: 8px;
            height: 8px
        }

        .scroll-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 9999px
        }

        .scroll-thin:hover::-webkit-scrollbar-thumb {
            background: #9ca3af
        }

        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="h-full overflow-hidden">
    {{-- FIXED SIDEBAR --}}
    @include('components.sidebar')

    {{-- MOBILE OVERLAY (sidebar open) --}}
    <div x-show="$store.layout.sidebarOpen" x-transition class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- APP SHELL (use h-screen + flex column; main will scroll) --}}
    <div class="h-screen w-full flex flex-col overflow-hidden transition-[padding] duration-300"
        :style="$store.layout.sidebarOpen ? 'padding-left: 16rem' : 'padding-left: 5rem'">

        {{-- TOP BAR (do not let it stretch) --}}
        <header class="h-16 flex-shrink-0 bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="$store.layout.toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 md:hidden"
                        aria-label="Toggle sidebar">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6h16M7 12h13M4 18h16" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                    <nav class="text-sm text-gray-500">
                        <span class="font-medium text-gray-800">@yield('crumb', 'Dashboard')</span>
                    </nav>
                </div>

                <div class="flex items-center gap-3" x-data="{ open: false }" @click.outside="open=false">
                    <button @click="open=!open" class="flex items-center gap-3 px-3 py-2 rounded-xl border bg-white">
                        <div class="w-8 h-8 rounded-full bg-gray-900 text-white grid place-items-center text-xs">
                            {{ strtoupper(auth()->user()->name[0] ?? 'U') }}
                        </div>
                        <div class="text-left hidden md:block">
                            <div class="text-sm font-medium truncate max-w-[14rem]">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 truncate max-w-[14rem]">{{ auth()->user()->email }}</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" x-transition
                        class="absolute top-14 right-4 w-56 bg-white border rounded-xl shadow-lg p-2 z-50">
                        <div class="px-3 py-2 text-xs text-gray-500">Signed in as</div>
                        <div class="px-3 pb-2 text-sm font-medium truncate">{{ auth()->user()->email }}</div>
                        <div class="border-t my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- MAIN (scrollable area) --}}
        <main class="flex-1 min-h-0 overflow-y-auto">
            <div class="max-w-7xl mx-auto p-6 pb-20">
                @if (session('status'))
                    <x-alert type="success" :message="session('status')" />
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
