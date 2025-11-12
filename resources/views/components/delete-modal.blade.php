<div x-data="{ open: false, url: '', force: false, title: 'Delete Confirmation', message: 'Are you sure you want to delete this item?' }"
    x-on:open-delete.window="open = true; url = $event.detail.url; force = $event.detail.force ?? false; title = $event.detail.title ?? 'Delete Confirmation'; message = $event.detail.message ?? message;"
    x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" x-on:click="open=false"></div>

    <div class="relative w-full max-w-md rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="title"></h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>

        <div class="mt-4 flex items-center gap-2" x-show="true">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                <input type="checkbox" x-model="force" class="rounded border-gray-300">
                Delete permanently (remove files)
            </label>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button
                class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                x-on:click="open=false" type="button">Cancel</button>

            <form method="POST" :action="url + (force ? '?force=1' : '')" x-ref="form">
                @csrf
                @method('DELETE')
                <button
                    class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                    type="submit">Delete</button>
            </form>
        </div>
    </div>
</div>
