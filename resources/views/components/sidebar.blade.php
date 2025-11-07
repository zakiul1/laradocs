@php
    $isSuper = auth()->user()->isSuperAdmin();
    $currentRoute = request()->route()->getName();

    $onUsers = \Illuminate\Support\Str::startsWith($currentRoute, 'admin.users.');
    $onFactories = request()->routeIs('admin.factories.*');
    $onFactoryCats = request()->routeIs('admin.categories.index') && request('scope') === 'factory';
@endphp

<!-- Optional: prevent FOUC for any [x-cloak] elements -->
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<!-- Wrap NAV + OVERLAY in the same Alpine scope -->
<div x-data="{
    sidebarOpen: @js(true),
    openUsers: @js($onUsers),
    openFactories: @js($onFactories || $onFactoryCats),
}" x-init="sidebarOpen = window.innerWidth > 1024" @resize.window="sidebarOpen = window.innerWidth > 1024">

    <nav :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 dark:bg-gray-900 dark:border-gray-800 flex flex-col transition-all duration-200"
        aria-label="Sidebar">
        <!-- Logo / Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-black dark:bg-white flex items-center justify-center">
                    <span class="text-white dark:text-black font-bold text-sm">A</span>
                </div>
                <span x-show="sidebarOpen" x-cloak class="font-semibold text-gray-900 dark:text-white">Admin</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden" aria-label="Toggle sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path :d="sidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>
        </div>

        <!-- Nav Items -->
        <div class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">

                <!-- Dashboard -->
                <li>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800
                               {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                    </x-nav-link>
                </li>

                {{-- FACTORIES (collapsible) --}}
                <li class="mt-4">
                    <button @click="openFactories = !openFactories"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800
                               {{ $onFactories || $onFactoryCats ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                        <div class="flex items-center gap-3">
                            {{-- lucide building icon (fallback inline) --}}
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" aria-hidden="true">
                                <path d="M3 21h18M6 21V3h12v18M9 8h6M9 12h6M9 16h6" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak class="text-gray-700 dark:text-gray-300">Factories</span>
                        </div>
                        <svg x-show="sidebarOpen" x-cloak :class="openFactories ? 'rotate-180' : ''"
                            class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" aria-hidden="true">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div x-cloak x-show="openFactories" x-transition
                        class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.factories.index') }}"
                            class="block pl-4 py-2 text-sm
                                  {{ request()->routeIs('admin.factories.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                  hover:text-gray-900 dark:hover:text-white">
                            All Factories
                        </a>

                        @if ($isSuper)
                            <a href="{{ route('admin.categories.index', ['scope' => 'factory']) }}"
                                class="block pl-4 py-2 text-sm
                                      {{ request()->routeIs('admin.categories.index') && request('scope') === 'factory' ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                      hover:text-gray-900 dark:hover:text-white">
                                Categories
                            </a>
                        @endif
                    </div>
                </li>

                <!-- Categories (direct link, super_admin only) -->
                @if ($isSuper)
                    <li class="mt-4">
                        <a href="{{ route('admin.categories.index', ['scope' => 'factory']) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->fullUrlIs('*admin/categories*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16M4 12h10M4 18h7" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak
                                class="text-gray-700 dark:text-gray-300">Categories</span>
                        </a>
                    </li>
                @endif

                {{-- Employees (super_admin only) --}}
                @if ($isSuper)
                    <div class="mt-6">
                        <p class="px-3 text-[10px] uppercase tracking-widest text-gray-400" x-show="sidebarOpen"
                            x-cloak>people</p>

                        <x-nav-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ request()->routeIs('admin.employees.*') ? 'bg-gray-100 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                                    stroke="currentColor" stroke-width="2" />
                                <path d="M7 10h5M7 14h8" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak>Employees</span>
                        </x-nav-link>
                    </div>
                @endif

                <!-- Users (Collapsible) -->
                <li>
                    <button @click="openUsers = !openUsers"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800
                               {{ $onUsers ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-gray-600 dark:text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak class="text-gray-700 dark:text-gray-300">Users</span>
                        </div>
                        <svg x-show="sidebarOpen" x-cloak :class="openUsers ? 'rotate-180' : ''"
                            class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <div x-cloak x-show="openUsers" x-transition
                        class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.users.index') }}"
                            class="block pl-4 py-2 text-sm {{ request()->routeIs('admin.users.index') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-gray-900 dark:hover:text-white">
                            All Users
                        </a>

                        @if ($isSuper)
                            <a href="{{ route('admin.users.create') }}"
                                class="block pl-4 py-2 text-sm {{ request()->routeIs('admin.users.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-gray-900 dark:hover:text-white">
                                Register Admin
                            </a>
                        @endif
                    </div>
                </li>

                <!-- Settings -->
                <li>
                    <x-nav-link href="#" :active="false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span x-show="sidebarOpen" x-cloak>Settings</span>
                    </x-nav-link>
                </li>
            </ul>
        </div>

        <!-- User -->
        <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ Str::upper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $isSuper ? 'Super Admin' : 'Admin' }}
                    </p>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay for mobile (same Alpine scope) -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition
        class="fixed inset-0 bg-black/50 z-40 lg:hidden" aria-hidden="true"></div>
</div>
