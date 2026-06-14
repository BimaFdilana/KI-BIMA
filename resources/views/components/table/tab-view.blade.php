@props(['tabs' => []])

<div class="w-full" x-data="{
    selectedTab: localStorage.getItem('currentTab') || 'categoryDataTable',
    switchTab(tabName) {
        this.selectedTab = tabName;
        localStorage.setItem('currentTab', tabName);
        window.dispatchEvent(new CustomEvent('toast', {
            detail: {
                message: 'Berhasil beralih ke tab ' + tabName,
                type: 'info'
            }
        }));
        this.$nextTick(() => {
            reloadTable(tabName);
        });
    }
}">
    <!-- Tab Card -->
    <div class="overflow-hidden pt-8">
        <!-- Tab Header -->
        <div class="pb-5">
            <div class="flex gap-2 overflow-x-auto rounded-lg bg-white p-2 shadow-lg" role="tablist" aria-label="tab options">
                @foreach ($tabs as $key => $tab)
                    <button @click="switchTab('{{ $key }}')" :aria-selected="selectedTab === '{{ $key }}'" :tabindex="selectedTab === '{{ $key }}' ? '0' : '-1'" :class="selectedTab === '{{ $key }}' ? 'bg-red-500 text-white' : 'text-red-600 bg-red-50 hover:bg-red-50 hover:text-red-700'" class="flex h-10 items-center rounded-lg px-4 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-red-50">
                        <i class="{{ $tab['icon'] ?? 'fa-solid mr-2 fa-circle' }}"></i>
                        <span>{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Tab Content -->
        @foreach ($tabs as $key => $tab)
            <div x-show="selectedTab === '{{ $key }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" id="tabpanel{{ ucfirst($key) }}" role="tabpanel" aria-label="{{ $key }}" x-cloak>
                <div class="">
                    {!! $tab['content'] !!}
                </div>
            </div>
        @endforeach
    </div>
</div>
