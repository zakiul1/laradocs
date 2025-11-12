@php
    $isSuper = auth()->user()->isSuperAdmin();
    $currentRoute = request()->route()->getName();

    $onUsers = \Illuminate\Support\Str::startsWith($currentRoute, 'admin.users.');
    $onFactories = request()->routeIs('admin.factories.*');
    $onFactoryCat = request()->routeIs('admin.factory-categories.*'); // module-scoped categories

    // Employees (super_admin only)
    $onEmployees = request()->routeIs('admin.employees.*');

    // Customers (super_admin only)
    $onCustomers = request()->routeIs('admin.customers.*');

    // Shippers (super_admin only)
    $onShippers = request()->routeIs('admin.shippers.*');

    // Banks (super_admin only)
    $onBanks = request()->routeIs('admin.banks.*');
@endphp

<style>
    [x-cloak] {
        display: none !important
    }
</style>

<div x-data="{
    sidebarOpen: @js(true),
    // 'Workspace' has no accordions yet, but we keep the layout store compatible
    // 'Management' accordions:
    openFactories: @js($onFactories || $onFactoryCat),
    openEmployees: @js($onEmployees),
    openCustomers: @js($onCustomers),
    openShippers: @js($onShippers),
    openBanks: @js($onBanks),
    openUsers: @js($onUsers),
}" x-init="sidebarOpen = window.innerWidth > 1024" @resize.window="sidebarOpen = window.innerWidth > 1024">

    <nav :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 dark:bg-gray-900 dark:border-gray-800 flex flex-col transition-all duration-200"
        aria-label="Sidebar">

        <!-- Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-black dark:bg-white grid place-items-center">
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

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">

                {{-- =======================
                     GROUP 1: WORKSPACE (TOP)
                   ======================= --}}
                <li x-show="sidebarOpen" x-cloak class="px-3 pt-1 pb-2">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400">Workspace</p>
                </li>

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

                {{-- Example placeholder for “Upcoming Modules” in Workspace --}}
                {{-- <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M12 6v12M6 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Upcoming Module</span>
                    </a>
                </li> --}}

                {{-- ===========================
                     GROUP 2: MANAGEMENT (BOTTOM)
                   =========================== --}}
                <li x-show="sidebarOpen" x-cloak class="px-3 pt-5 pb-2">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400">Management</p>
                </li>

                {{-- Factory (collapsible) --}}
                <li>
                    <button @click="openFactories = !openFactories"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800
                               {{ $onFactories || $onFactoryCat ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M3 21h18M6 21V3h12v18M9 8h6M9 12h6M9 16h6" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span x-show="sidebarOpen" x-cloak class="text-gray-700 dark:text-gray-300">Factory</span>
                        </div>
                        <svg x-show="sidebarOpen" x-cloak :class="openFactories ? 'rotate-180' : ''"
                            class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div x-cloak x-show="openFactories" x-transition
                        class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.factories.index') }}"
                            class="block pl-4 py-2 text-sm
                                  {{ request()->routeIs('admin.factories.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                  hover:text-gray-900 dark:hover:text-white">All
                            Factories</a>

                        <a href="{{ route('admin.factory-categories.index') }}"
                            class="block pl-4 py-2 text-sm
                                  {{ request()->routeIs('admin.factory-categories.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                  hover:text-gray-900 dark:hover:text-white">Factory
                            Categories</a>
                    </div>
                </li>

                {{-- Employees --}}
                @if ($isSuper)
                    <li>
                        <button @click="openEmployees = !openEmployees"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ $onEmployees ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path d="M16 11a4 4 0 10-8 0 4 4 0 008 0Z" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M6 21v-1a6 6 0 0112 0v1" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak
                                    class="text-gray-700 dark:text-gray-300">Employees</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="openEmployees ? 'rotate-180' : ''"
                                class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-cloak x-show="openEmployees" x-transition
                            class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.employees.index') }}"
                                class="block pl-4 py-2 text-sm
                                      {{ request()->routeIs('admin.employees.index') || request()->routeIs('admin.employees.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                      hover:text-gray-900 dark:hover:text-white">All
                                Employees</a>

                            <a href="{{ route('admin.employees.create') }}"
                                class="block pl-4 py-2 text-sm
                                      {{ request()->routeIs('admin.employees.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                      hover:text-gray-900 dark:hover:text-white">Add
                                Employee</a>
                        </div>
                    </li>
                @endif

                {{-- Customers --}}
                @if ($isSuper)
                    <li>
                        <button @click="openCustomers = !openCustomers"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ $onCustomers ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path d="M20 21v-1a6 6 0 00-9-5.197M9 10a4 4 0 100-8 4 4 0 000 8z" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak
                                    class="text-gray-700 dark:text-gray-300">Customers</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="openCustomers ? 'rotate-180' : ''"
                                class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-cloak x-show="openCustomers" x-transition
                            class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.customers.index') }}"
                                class="block pl-4 py-2 text-sm
                                      {{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                      hover:text-gray-900 dark:hover:text-white">All
                                Customers</a>

                            <a href="{{ route('admin.customers.create') }}"
                                class="block pl-4 py-2 text-sm
                                      {{ request()->routeIs('admin.customers.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                      hover:text-gray-900 dark:hover:text-white">Add
                                Customer</a>
                        </div>
                    </li>
                @endif

                {{-- Shippers --}}
                @if ($isSuper)
                    <li>
                        <button @click="openShippers = !openShippers"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ $onShippers ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <path d="M3 7h18M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak
                                    class="text-gray-700 dark:text-gray-300">Shippers</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="openShippers ? 'rotate-180' : ''"
                                class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-cloak x-show="openShippers" x-transition
                            class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.shippers.index') }}"
                                class="block pl-4 py-2 text-sm
                                   {{ request()->routeIs('admin.shippers.index') || request()->routeIs('admin.shippers.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                   hover:text-gray-900 dark:hover:text-white">All
                                Shippers</a>

                            <a href="{{ route('admin.shippers.create') }}"
                                class="block pl-4 py-2 text-sm
                                   {{ request()->routeIs('admin.shippers.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                   hover:text-gray-900 dark:hover:text-white">Add
                                Shipper</a>
                        </div>
                    </li>
                @endif

                {{-- Banks --}}
                @if ($isSuper)
                    <li>
                        <button @click="openBanks = !openBanks"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ $onBanks ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <path d="M3 10l9-7 9 7v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8z" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9 22V12h6v10" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak
                                    class="text-gray-700 dark:text-gray-300">Banks</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="openBanks ? 'rotate-180' : ''"
                                class="w-4 h-4 text-gray-500 transition-transform" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-cloak x-show="openBanks" x-transition
                            class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.banks.index') }}"
                                class="block pl-4 py-2 text-sm
                                   {{ request()->routeIs('admin.banks.index') || request()->routeIs('admin.banks.*') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                   hover:text-gray-900 dark:hover:text-white">All
                                Banks</a>
                            <a href="{{ route('admin.banks.create') }}"
                                class="block pl-4 py-2 text-sm
                                   {{ request()->routeIs('admin.banks.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}
                                   hover:text-gray-900 dark:hover:text-white">Add
                                Bank</a>
                        </div>
                    </li>
                @endif

                {{-- Users --}}
                <li>
                    <button @click="openUsers = !openUsers"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               hover:bg-gray-100 dark:hover:bg-gray-800
                               {{ $onUsers ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-gray-600 dark:text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1m0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
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

                    <div x-cloak x-show="openUsers" x-transition
                        class="mt-1 ml-8 space-y-1 border-l-2 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.users.index') }}"
                            class="block pl-4 py-2 text-sm {{ request()->routeIs('admin.users.index') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-gray-900 dark:hover:text-white">
                            All Users</a>

                        @if ($isSuper)
                            <a href="{{ route('admin.users.create') }}"
                                class="block pl-4 py-2 text-sm {{ request()->routeIs('admin.users.create') ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }} hover:text-gray-900 dark:hover:text-white">
                                Register Admin</a>
                        @endif
                    </div>
                </li>
            </ul>
        </div>

        <!-- Footer / User -->
        <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gray-300 dark:bg-gray-700 grid place-items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ Str::upper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $isSuper ? 'Super Admin' : 'Admin' }}</p>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition
        class="fixed inset-0 bg-black/50 z-40 lg:hidden" aria-hidden="true"></div>
</div>
