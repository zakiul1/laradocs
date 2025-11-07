@props([
    // module scope, e.g. 'factory', 'buyer', 'bank', 'employee'
    'scope' => 'factory',
    // selected category IDs (array|Collection)
    'selected' => [],
])

@php
    $options = \App\Models\Category::flatForSelect($scope);
@endphp

<div x-data="categoryPicker()" x-init="init(@js($scope), @js($options), @js($selected))" class="space-y-2">
    <x-label value="Categories" />
    <div class="flex gap-2">
        <input x-model="query" type="text" placeholder="Search categories…" class="w-full rounded-md border px-3 py-2">
        <button type="button" @click="open = true" class="px-3 py-2 rounded-md border bg-white hover:bg-gray-50">
            + Add new
        </button>
    </div>

    <div class="max-h-48 overflow-auto rounded-md border">
        <template x-for="opt in filtered" :key="opt.id">
            <label class="flex items-center gap-2 px-3 py-2 border-b last:border-0">
                <input type="checkbox" class="rounded" :value="opt.id" name="category_ids[]"
                    :checked="selected.has(opt.id)" @change="toggle(opt.id)">
                <span class="text-sm" x-text="opt.label"></span>
            </label>
        </template>
        <p class="p-3 text-sm text-gray-500" x-show="!filtered.length">No categories found.</p>
    </div>

    {{-- Modal: quick create --}}
    <div x-cloak x-show="open" class="fixed inset-0 bg-black/40 grid place-items-center z-50">
        <div class="bg-white rounded-xl shadow p-6 w-full max-w-lg">
            <h3 class="text-base font-semibold mb-3">Add New Category</h3>
            <div class="space-y-3">
                <div>
                    <x-label value="Name" />
                    <x-input x-model="form.name" placeholder="e.g. Compliance" />
                </div>
                <div>
                    <x-label value="Parent (optional)" />
                    <select x-model="form.parent_id" class="w-full rounded-md border px-3 py-2">
                        <option value="">None</option>
                        <template x-for="[id, label] of Object.entries(options)" :key="id">
                            <option :value="id" x-text="label"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" class="px-4 py-2 rounded-md border bg-white hover:bg-gray-50"
                    @click="open=false">Cancel</button>
                <button type="button" class="px-4 py-2 rounded-md bg-gray-900 text-white"
                    @click="create()">Create</button>
            </div>
        </div>
    </div>

    <script>
        function categoryPicker() {
            return {
                scope: 'factory',
                options: {}, // {id: label}
                list: [], // [{id,label}]
                selected: new Set(),
                query: '',
                open: false,
                form: {
                    name: '',
                    parent_id: ''
                },

                init(scope, options, selected) {
                    this.scope = scope;
                    this.options = options;
                    this.list = Object.entries(options).map(([id, label]) => ({
                        id: Number(id),
                        label
                    }));
                    (selected || []).forEach(id => this.selected.add(Number(id)));
                },
                get filtered() {
                    const q = this.query.toLowerCase();
                    return this.list.filter(x => x.label.toLowerCase().includes(q));
                },
                toggle(id) {
                    id = Number(id);
                    if (this.selected.has(id)) this.selected.delete(id);
                    else this.selected.add(id);
                },
                async create() {
                    if (!this.form.name.trim()) return;
                    try {
                        const res = await fetch('{{ route('admin.categories.quick-create') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                scope: this.scope,
                                name: this.form.name,
                                parent_id: this.form.parent_id || null,
                            }),
                        });
                        if (!res.ok) throw new Error('Failed');
                        const data = await res.json();
                        // update options locally
                        this.options[data.id] = this.form.parent_id ? '— ' + this.form.name : this.form.name;
                        this.list.push({
                            id: data.id,
                            label: this.options[data.id]
                        });
                        this.selected.add(Number(data.id));
                        this.form = {
                            name: '',
                            parent_id: ''
                        };
                        this.open = false;
                    } catch (e) {
                        alert('Could not create category.');
                    }
                }
            }
        }
    </script>
</div>
