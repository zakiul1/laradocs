@extends('layouts.app')

@section('content')
    <div x-data="factoryTaxUI()" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        <h1 class="mb-5 text-xl font-semibold text-gray-900 dark:text-white">Factory Categories</h1>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div
                class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left: Add (Parent or Child) --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h5 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">Add New Category</h5>

                {{-- This single form can create a Parent or a Child
                     - No Parent  -> admin.factory-categories.store
                     - Has Parent -> admin.factory-subcategories.store
                --}}
                <form id="wp-tax-form" method="post" action="{{ route('admin.factory-categories.store') }}"
                    class="space-y-4">
                    @csrf

                    @php
                        // For parent dropdown: prefer $allCategories if controller sent it,
                        // otherwise use unpaginated $categories->getCollection() if it's a paginator,
// or just $categories when it's a plain collection.
                        $categoryOptions = isset($allCategories)
                            ? $allCategories
                            : (method_exists($categories, 'getCollection')
                                ? $categories->getCollection()
                                : $categories);
                    @endphp

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Parent
                            (optional)</label>
                        <select id="parent_select" name="factory_category_id"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <option value="">— No Parent (create main category) —</option>
                            @foreach ($categoryOptions as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Leave empty to create a <strong>parent</strong> category. Select a parent to create a
                            <strong>child</strong> (subcategory).
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input name="name" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                        <input type="number" name="position" value="0" min="0"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>

                    <button
                        class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-auto">
                        Add Category
                    </button>
                </form>

                {{-- Toggle target route based on parent selection --}}
                <script>
                    (function() {
                        const form = document.getElementById('wp-tax-form');
                        const select = document.getElementById('parent_select');
                        const parentRoute = @json(route('admin.factory-categories.store'));
                        const childRoute = @json(route('admin.factory-subcategories.store'));

                        const updateAction = () => form.action = select.value ? childRoute : parentRoute;
                        updateAction();
                        select.addEventListener('change', updateAction);

                        form.addEventListener('submit', () => {
                            if (!select.value) {
                                // Creating parent: remove the name to avoid sending unexpected key
                                select.setAttribute('data-original-name', select.getAttribute('name'));
                                select.removeAttribute('name');
                            } else if (!select.getAttribute('name')) {
                                // Creating child: ensure the name attribute exists
                                select.setAttribute('name', select.getAttribute('data-original-name') ||
                                    'factory_category_id');
                            }
                        });
                    })();
                </script>
            </div>

            {{-- Right: Tabs for Categories & Subcategories --}}
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    {{-- Tabs --}}
                    <div
                        class="flex items-center gap-2 border-b border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                        <button type="button"
                            :class="tab === 'categories' ? 'bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400' :
                                'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/40'"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium" @click="tab='categories'">
                            Categories
                        </button>
                        <button type="button"
                            :class="tab === 'subcategories' ? 'bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400' :
                                'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/40'"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium" @click="tab='subcategories'">
                            Subcategories
                        </button>
                    </div>

                    {{-- Tab: Categories table --}}
                    <div x-show="tab==='categories'">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Name
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Parent</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Position</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Updated</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($categories as $cat)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                                {{ $cat->name }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                {{ optional($cat->parent)->name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                {{ $cat->position }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                                {{ $cat->updated_at->diffForHumans() }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2">
                                                    <form method="post"
                                                        action="{{ route('admin.factory-categories.update', $cat) }}"
                                                        class="flex flex-wrap items-center gap-2">
                                                        @csrf @method('PUT')

                                                        <select name="factory_category_id"
                                                            class="w-44 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                                            <option value="">— No Parent —</option>
                                                            @foreach ($categoryOptions as $p)
                                                                @if ($p->id !== $cat->id)
                                                                    <option value="{{ $p->id }}"
                                                                        @selected(optional($cat->parent)->id === $p->id)>{{ $p->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>

                                                        <input type="text" name="name" value="{{ $cat->name }}"
                                                            class="w-44 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                        <input type="number" name="position" value="{{ $cat->position }}"
                                                            class="w-24 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                        <button
                                                            class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                                            Save
                                                        </button>
                                                    </form>

                                                    <button @click="openDelete(@js(route('admin.factory-categories.destroy', $cat)))"
                                                        class="inline-flex items-center rounded-lg border border-red-500 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-900/30">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No categories yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            {{ $categories->links() }}
                        </div>
                    </div>

                    {{-- Tab: Subcategories table --}}
                    <div x-show="tab==='subcategories'">
                        @php
                            // Build a subcategory list if controller didn't send $subcategories.
// We assume each $c has a `children` relation; if not available, this stays empty.
$fallbackSubs = collect();
foreach ($categoryOptions as $c) {
    if (method_exists($c, 'children') && $c->relationLoaded('children')) {
                                    $fallbackSubs = $fallbackSubs->merge($c->children);
                                }
                            }
                            $subRows = isset($subcategories) ? $subcategories : $fallbackSubs;
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Subcategory</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Parent</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Position</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                            Updated</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($subRows as $sub)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                                {{ $sub->name }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                {{ optional($sub->category ?? $sub->parent)->name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                {{ $sub->position }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                                {{ $sub->updated_at->diffForHumans() }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap items-center justify-end gap-2">
                                                    <form method="post"
                                                        action="{{ route('admin.factory-subcategories.update', $sub) }}"
                                                        class="flex flex-wrap items-center gap-2">
                                                        @csrf @method('PUT')

                                                        <select name="factory_category_id"
                                                            class="w-44 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                                            @foreach ($categoryOptions as $p)
                                                                <option value="{{ $p->id }}"
                                                                    @selected(($sub->factory_category_id ?? ($sub->parent_id ?? null)) == $p->id)>{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <input type="text" name="name" value="{{ $sub->name }}"
                                                            class="w-40 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                        <input type="number" name="position"
                                                            value="{{ $sub->position }}"
                                                            class="w-20 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                        <button
                                                            class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                                            Save
                                                        </button>
                                                    </form>

                                                    <button @click="openDelete(@js(route('admin.factory-subcategories.destroy', $sub)))"
                                                        class="inline-flex items-center rounded-lg border border-red-500 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-900/30">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No subcategories yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- If $subcategories is a paginator, show links; otherwise hide --}}
                        @if (isset($subcategories) && method_exists($subcategories, 'links'))
                            <div class="border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                {{ $subcategories->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Global confirm modal (Tailwind, Alpine) --}}
        <div x-show="confirm.open" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none">
            <div x-transition class="w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Please confirm</h3>
                    <button @click="confirm.open=false"
                        class="rounded p-1 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">✕</button>
                </div>

                <div class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete this item? This action cannot be undone.
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-5 py-3 dark:border-gray-800">
                    <button @click="confirm.open=false"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <form :action="confirm.action" method="post">
                        @csrf @method('DELETE')
                        <button
                            class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function factoryTaxUI() {
            return {
                tab: 'categories',
                confirm: {
                    open: false,
                    action: ''
                },
                openDelete(action) {
                    this.confirm.action = action;
                    this.confirm.open = true;
                }
            }
        }
    </script>
@endsection
