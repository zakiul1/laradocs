{{-- resources/views/components/company-picker.blade.php --}}
<script>
    function companyPicker({
        initialId = null,
        initialName = '',
        initialType = ''
    } = {}) {
        return {
            open: false,
            type: initialType || '', // 'customer' | 'shipper'
            q: '',
            items: [],
            page: 1,
            next: null,
            loading: false,
            selectedId: initialId,
            selectedName: initialName,

            setType(t) {
                this.type = t;
                // reset on type change
                this.selectedId = null;
                this.selectedName = '';
                this.q = '';
                this.items = [];
                this.page = 1;
                this.next = null;
            },

            async search(debounced = true) {
                if (!this.type) {
                    this.items = [];
                    return;
                }
                const run = async () => {
                    this.loading = true;
                    try {
                        const url = new URL(@json(route('admin.banks.company-options')), window.location.origin);
                        url.searchParams.set('type', this.type);
                        url.searchParams.set('q', this.q);
                        url.searchParams.set('page', this.page);
                        const res = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const json = await res.json();
                        this.items = this.page === 1 ? json.data : [...this.items, ...json.data];
                        this.next = json.next;
                    } finally {
                        this.loading = false;
                    }
                };

                if (debounced) {
                    clearTimeout(this._t);
                    this._t = setTimeout(run, 250);
                } else {
                    await run();
                }
            },

            select(item) {
                this.selectedId = item.id;
                this.selectedName = item.name;
                this.open = false;
            },

            clear() {
                this.selectedId = null;
                this.selectedName = '';
                this.q = '';
                this.items = [];
                this.page = 1;
                this.next = null;
            },

            more() {
                if (this.next) {
                    this.page = this.next;
                    this.search(false);
                }
            }
        }
    }
</script>
