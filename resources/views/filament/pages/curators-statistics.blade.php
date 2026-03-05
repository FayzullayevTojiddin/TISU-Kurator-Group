<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ===== OY VA YIL TANLASH ===== --}}
        <div class="grid grid-cols-7 gap-2" style="grid-template-rows: auto auto;">
            @foreach([1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel', 5 => 'May', 6 => 'Iyun'] as $num => $name)
                <button
                    wire:click="selectMonth({{ $num }})"
                    @class([
                        'py-3 px-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer',
                        'bg-primary-600 text-white shadow-md ring-2 ring-primary-400' => $selectedMonth === $num,
                        'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedMonth !== $num,
                    ])
                >{{ $name }}</button>
            @endforeach

            <div class="row-span-2 flex flex-col items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg min-h-[100px]">
                <button wire:click="selectYear({{ $selectedYear + 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <span class="text-2xl font-bold text-gray-800 dark:text-gray-200 my-1">{{ $selectedYear }}</span>
                <button wire:click="selectYear({{ $selectedYear - 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            @foreach([7 => 'Iyul', 8 => 'Avgust', 9 => 'Sentabr', 10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr'] as $num => $name)
                <button
                    wire:click="selectMonth({{ $num }})"
                    @class([
                        'py-3 px-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer',
                        'bg-primary-600 text-white shadow-md ring-2 ring-primary-400' => $selectedMonth === $num,
                        'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedMonth !== $num,
                    ])
                >{{ $name }}</button>
            @endforeach
        </div>

        {{-- ===== UMUMIY STATISTIKA KARTOCHKALARI ===== --}}
        <div class="grid grid-cols-4 gap-3">
            <div class="rounded-xl p-4 text-center border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $this->summary['total'] }}</div>
                <div class="text-xs font-medium text-gray-500 mt-1">Jami kuratorlar</div>
            </div>
            <div class="rounded-xl p-4 text-center border-2 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
                <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->summary['good'] }}</div>
                <div class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-1">Yaxshi</div>
            </div>
            <div class="rounded-xl p-4 text-center border-2 border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $this->summary['neutral'] }}</div>
                <div class="text-xs font-medium text-orange-700 dark:text-orange-300 mt-1">O'rtacha</div>
            </div>
            <div class="rounded-xl p-4 text-center border-2 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20">
                <div class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $this->summary['bad'] }}</div>
                <div class="text-xs font-medium text-rose-700 dark:text-rose-300 mt-1">Yomon</div>
            </div>
        </div>

        {{-- ===== FAKULTET FILTERI ===== --}}
        @if($this->faculties->count() > 1)
            <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-400 dark:text-gray-500 font-medium mb-2">Fakultet:</div>
                <div class="grid grid-cols-{{ min($this->faculties->count() + 1, 5) }} gap-2">
                    <button
                        wire:click="selectFaculty(null)"
                        @class([
                            'px-4 py-2.5 rounded-lg text-sm font-medium transition-all text-center cursor-pointer',
                            'bg-primary-600 text-white shadow-sm' => is_null($selectedFacultyId),
                            'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600' => !is_null($selectedFacultyId),
                        ])
                    >Barchasi</button>
                    @foreach($this->faculties as $faculty)
                        <button
                            wire:click="selectFaculty({{ $faculty->id }})"
                            @class([
                                'px-4 py-2.5 rounded-lg text-sm font-medium transition-all text-center cursor-pointer',
                                'bg-primary-600 text-white shadow-sm' => $selectedFacultyId === $faculty->id,
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600' => $selectedFacultyId !== $faculty->id,
                            ])
                        >{{ $faculty->name }}</button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== KURATORLAR JADVALI ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Kurator</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Fakultet</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400">Guruh(lar)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">Jami</th>
                        <th class="px-4 py-3 text-center font-semibold text-emerald-600 dark:text-emerald-400">Bajarildi</th>
                        <th class="px-4 py-3 text-center font-semibold text-rose-600 dark:text-rose-400">Bajarilmadi</th>
                        <th class="px-4 py-3 text-center font-semibold text-sky-600 dark:text-sky-400">Tekshiruvda</th>
                        <th class="px-4 py-3 text-center font-semibold text-orange-600 dark:text-orange-400">Rad etildi</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400">Bajarilish %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->curators as $index => $curator)
                        <tr @class([
                            'transition-colors',
                            'bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30' => $curator->performance === 'good',
                            'bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/30' => $curator->performance === 'bad',
                            'hover:bg-gray-50 dark:hover:bg-gray-700' => $curator->performance === 'neutral',
                        ])>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $curator->user->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $curator->faculty_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $curator->group_names }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">{{ $curator->total }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-emerald-600 dark:text-emerald-400">{{ $curator->completed }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-rose-600 dark:text-rose-400">{{ $curator->not_completed }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-sky-600 dark:text-sky-400">{{ $curator->under_review }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-orange-600 dark:text-orange-400">{{ $curator->rejected }}</td>
                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' => $curator->performance === 'good',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' => $curator->performance === 'bad',
                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $curator->performance === 'neutral',
                                ])>
                                    {{ $curator->completion_rate }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Bu oy uchun ma'lumot topilmadi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
