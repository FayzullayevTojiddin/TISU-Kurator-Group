<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
        {{-- Jami --}}
        <div class="relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg" style="background-color: rgba(var(--primary-50), 1); /* fallback */" >
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5" style="color: rgb(var(--primary-600));" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jami</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $total }}</p>
                </div>
            </div>
            @if($total > 0)
                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                        <span>Bajarilish</span>
                        <span class="font-semibold" style="color: rgb(var(--primary-600));">{{ $percentage }}%</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700">
                        <div class="h-1.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background-color: rgb(var(--primary-600));"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Bajarildi --}}
        <div class="relative flex items-center overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-50 dark:bg-green-500/10">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bajarildi</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $completed }}</p>
                </div>
            </div>
        </div>

        {{-- Tekshiruvda --}}
        <div class="relative flex items-center overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                    <x-heroicon-o-clock class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tekshiruvda</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $underReview }}</p>
                </div>
            </div>
        </div>

        {{-- Bajarilmadi --}}
        <div class="relative flex items-center overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10">
                    <x-heroicon-o-x-circle class="h-5 w-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bajarilmadi</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $notCompleted }}</p>
                </div>
            </div>
        </div>

        {{-- Rad etildi --}}
        <div class="relative flex items-center overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-500/10">
                    <x-heroicon-o-no-symbol class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rad etildi</p>
                    <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $rejected }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
