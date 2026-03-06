<x-filament-panels::page>
    @if(auth()->user()->isCurator())
        {{-- ==================== KURATOR PANELI ==================== --}}
        <div class="space-y-6">

            {{-- TAB FILTERLARI --}}
            <div class="grid grid-cols-5 gap-3">
                @php
                    $tabs = [
                        'all' => ['label' => 'Barchasi', 'color' => 'gray'],
                        'not_completed' => ['label' => 'Topshirilmagan', 'color' => 'rose'],
                        'under_review' => ['label' => 'Tekshiruvda', 'color' => 'amber'],
                        'completed' => ['label' => 'Tasdiqlangan', 'color' => 'emerald'],
                        'rejected' => ['label' => 'Rad etilgan', 'color' => 'red'],
                    ];
                @endphp

                @foreach($tabs as $key => $tab)
                    <button
                        wire:click="selectCuratorTab('{{ $key }}')"
                        @class([
                            'relative flex flex-col items-center py-4 px-3 rounded-xl font-medium transition-all duration-200 cursor-pointer',
                            'bg-primary-600 text-white shadow-lg ring-2 ring-primary-400' => $curatorTab === $key,
                            'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:shadow-md' => $curatorTab !== $key,
                        ])
                    >
                        <span class="text-2xl font-bold">{{ $this->curatorCounts[$key] }}</span>
                        <span class="text-xs mt-1">{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- TOPSHIRIQLAR RO'YXATI --}}
            <div class="space-y-3">
                @forelse($this->filteredCuratorSubmissions as $submission)
                    <a
                        href="{{ route('filament.admin.resources.task-submissions.edit', $submission) }}"
                        wire:navigate
                        @class([
                            'flex items-center justify-between p-4 rounded-xl bg-white dark:bg-gray-800 border-l-4 transition-all duration-200 cursor-pointer hover:shadow-lg',
                            'border-l-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' => $submission->status === \App\Enums\TaskStatus::Completed,
                            'border-l-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20' => $submission->status === \App\Enums\TaskStatus::NotCompleted,
                            'border-l-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20' => $submission->status === \App\Enums\TaskStatus::UnderReview,
                            'border-l-red-500 hover:bg-red-50 dark:hover:bg-red-900/20' => $submission->status === \App\Enums\TaskStatus::Rejected,
                        ])
                    >
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $submission->task?->title ?? '—' }}
                                </h3>
                                <span @class([
                                    'shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $submission->status === \App\Enums\TaskStatus::Completed,
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' => $submission->status === \App\Enums\TaskStatus::NotCompleted,
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' => $submission->status === \App\Enums\TaskStatus::UnderReview,
                                    'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' => $submission->status === \App\Enums\TaskStatus::Rejected,
                                ])>
                                    {{ $submission->status->label() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $submission->group?->name }}</span>
                                <span>{{ $submission->task?->week?->title }}</span>
                                @if($submission->submitted_at)
                                    <span>{{ $submission->submitted_at->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" class="shrink-0 ml-4 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @empty
                    <div class="text-center py-16 text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Topshiriqlar topilmadi
                    </div>
                @endforelse
            </div>
        </div>

    @elseif(auth()->user()->isDean())
        {{-- ==================== DEKAN PANELI ==================== --}}
        <div class="space-y-6">

            {{-- FAKULTET NOMI --}}
            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-400 dark:text-gray-500 font-medium mb-1">Fakultet:</div>
                <div class="px-4 py-2.5 rounded-lg text-sm font-medium bg-primary-600 text-white inline-block">
                    {{ $this->faculties->first()?->name }}
                </div>
            </div>

            {{-- OY VA YIL TANLASH --}}
            <div class="grid grid-cols-7 gap-2" style="grid-template-rows: auto auto;">
                @foreach([1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel', 5 => 'May', 6 => 'Iyun'] as $num => $name)
                    <button
                        wire:click="selectMonth({{ $num }})"
                        @class([
                            'py-3 px-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer',
                            'bg-primary-600 text-white shadow-md ring-2 ring-primary-400' => $selectedMonth === $num,
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedMonth !== $num,
                        ])
                    >
                        {{ $name }}
                    </button>
                @endforeach

                <div class="row-span-2 flex flex-col items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg min-h-[100px]">
                    <button wire:click="selectYear({{ $selectedYear + 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <span class="text-2xl font-bold text-gray-800 dark:text-gray-200 my-1">{{ $selectedYear }}</span>
                    <button wire:click="selectYear({{ $selectedYear - 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors">
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
                    >
                        {{ $name }}
                    </button>
                @endforeach
            </div>

            {{-- HAFTA BLOKLARI --}}
            <div class="grid grid-cols-4 gap-4">
                @foreach(range(1, 4) as $weekNum)
                    @php $week = $this->weeks->firstWhere('week_number', $weekNum); @endphp
                    <div class="min-h-[1px]">
                        @if($week)
                            <button
                                wire:click="selectWeek({{ $week->id }})"
                                @class([
                                    'w-full text-left p-5 rounded-xl border-2 transition-all duration-200 cursor-pointer relative overflow-hidden',
                                    'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg ring-1 ring-primary-300' => $selectedWeekId === $week->id,
                                    'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-primary-300 dark:hover:border-gray-500 hover:shadow-md' => $selectedWeekId !== $week->id,
                                ])
                            >
                                <span class="absolute top-2 right-3 text-4xl font-black opacity-10 select-none">{{ $weekNum }}</span>
                                <div @class([
                                    'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold mb-2',
                                    'bg-primary-600 text-white' => $selectedWeekId === $week->id,
                                    'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' => $selectedWeekId !== $week->id,
                                ])>
                                    {{ $weekNum }}
                                </div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $week->title }}</h3>
                                @if($week->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $week->description }}</p>
                                @endif
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($this->weeks->isEmpty())
                <div class="text-center py-10 text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Bu oy uchun haftalar topilmadi
                </div>
            @endif

            {{-- HAFTA TANLANGANDA STATISTIKA --}}
            @if($selectedWeekId)
                <div class="space-y-4">
                    {{-- KUN TANLASH --}}
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($this->days as $dayNum => $dayName)
                            <button
                                wire:click="selectDay({{ $dayNum }})"
                                @class([
                                    'py-3 px-3 rounded-lg text-sm font-medium text-center transition-all duration-200 cursor-pointer',
                                    'bg-primary-600 text-white shadow-md' => $selectedDay === $dayNum,
                                    'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedDay !== $dayNum,
                                ])
                            >
                                {{ $dayName }}
                            </button>
                        @endforeach
                    </div>

                    {{-- STATISTIKA --}}
                    <div class="grid grid-cols-4 gap-3">
                        <div class="rounded-xl p-4 text-center border-2 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
                            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->stats['completed'] }}</div>
                            <div class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-1">Bajarildi</div>
                        </div>
                        <div class="rounded-xl p-4 text-center border-2 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20">
                            <div class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $this->stats['not_completed'] }}</div>
                            <div class="text-xs font-medium text-rose-700 dark:text-rose-300 mt-1">Bajarilmadi</div>
                        </div>
                        <div class="rounded-xl p-4 text-center border-2 border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20">
                            <div class="text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $this->stats['under_review'] }}</div>
                            <div class="text-xs font-medium text-sky-700 dark:text-sky-300 mt-1">Tekshiruvda</div>
                        </div>
                        <div class="rounded-xl p-4 text-center border-2 border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20">
                            <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $this->stats['rejected'] }}</div>
                            <div class="text-xs font-medium text-orange-700 dark:text-orange-300 mt-1">Rad etildi</div>
                        </div>
                    </div>

                    {{-- EXCEL YUKLAB OLISH --}}
                    @if($this->allGroups->isNotEmpty())
                        <div class="flex justify-end">
                            <button
                                wire:click="exportExcel"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Excel yuklab olish
                            </button>
                        </div>
                    @endif

                    {{-- STATUS FILTERLARI --}}
                    <div class="grid grid-cols-5 gap-2">
                        <button wire:click="selectStatus('all')"
                            @class([
                                'flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-all cursor-pointer',
                                'bg-primary-600 text-white shadow-md' => $selectedStatus === 'all',
                                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' => $selectedStatus !== 'all',
                            ])>
                            Barchasi
                            <span @class([
                                'inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold',
                                'bg-white/20 text-white' => $selectedStatus === 'all',
                                'bg-gray-100 dark:bg-gray-700 text-gray-500' => $selectedStatus !== 'all',
                            ])>{{ $this->statusCounts['all'] }}</span>
                        </button>

                        @php
                            $statusButtons = [
                                'completed' => ['label' => 'Bajarildi', 'color' => 'emerald'],
                                'not_completed' => ['label' => 'Bajarilmadi', 'color' => 'rose'],
                                'under_review' => ['label' => 'Tekshiruvda', 'color' => 'sky'],
                                'rejected' => ['label' => 'Rad etildi', 'color' => 'orange'],
                            ];
                        @endphp

                        @foreach($statusButtons as $statusKey => $btn)
                            <button wire:click="selectStatus('{{ $statusKey }}')"
                                @class([
                                    'flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-all cursor-pointer',
                                    "bg-{$btn['color']}-600 text-white shadow-md" => $selectedStatus === $statusKey,
                                    "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-{$btn['color']}-50 dark:hover:bg-gray-700" => $selectedStatus !== $statusKey,
                                ])>
                                {{ $btn['label'] }}
                                <span @class([
                                    'inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold',
                                    'bg-white/20 text-white' => $selectedStatus === $statusKey,
                                    "bg-{$btn['color']}-100 dark:bg-{$btn['color']}-900/30 text-{$btn['color']}-700 dark:text-{$btn['color']}-400" => $selectedStatus !== $statusKey,
                                ])>{{ $this->statusCounts[$statusKey] }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- GURUHLAR --}}
                    <div class="max-h-[520px] overflow-y-auto rounded-xl pr-1">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @forelse($this->groups as $group)
                            <a
                                href="{{ route('filament.admin.resources.groups.edit', $group) }}"
                                wire:navigate
                                @class([
                                    'block p-4 rounded-xl border-l-4 transition-all duration-200 hover:shadow-lg group bg-white dark:bg-gray-800',
                                    'border-l-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30' => $group->aggregate_status === 'completed',
                                    'border-l-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30' => $group->aggregate_status === 'not_completed',
                                    'border-l-sky-500 hover:bg-sky-50 dark:hover:bg-sky-900/30' => $group->aggregate_status === 'under_review',
                                    'border-l-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/30' => $group->aggregate_status === 'rejected',
                                ])
                            >
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200 truncate group-hover:text-primary-600">
                                    {{ $group->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                    {{ $group->curator?->name ?? '—' }}
                                </p>
                                <span @class([
                                    'inline-block mt-2 px-2.5 py-1 rounded-md text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' => $group->aggregate_status === 'completed',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' => $group->aggregate_status === 'not_completed',
                                    'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300' => $group->aggregate_status === 'under_review',
                                    'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' => $group->aggregate_status === 'rejected',
                                ])>
                                    @switch($group->aggregate_status)
                                        @case('completed') Bajarildi @break
                                        @case('not_completed') Bajarilmadi @break
                                        @case('under_review') Tekshiruvda @break
                                        @case('rejected') Rad etildi @break
                                    @endswitch
                                </span>
                            </a>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-400 dark:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Guruhlar topilmadi
                            </div>
                        @endforelse
                    </div>
                    </div>
                </div>
            @endif
        </div>

    @else
        {{-- ==================== ADMIN PANELI ==================== --}}
        <div class="space-y-6">

            {{-- OY VA YIL TANLASH --}}
            <div class="grid grid-cols-7 gap-2" style="grid-template-rows: auto auto;">
                @foreach([1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel', 5 => 'May', 6 => 'Iyun'] as $num => $name)
                    <button
                        wire:click="selectMonth({{ $num }})"
                        @class([
                            'py-3 px-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer',
                            'bg-primary-600 text-white shadow-md ring-2 ring-primary-400' => $selectedMonth === $num,
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedMonth !== $num,
                        ])
                    >
                        {{ $name }}
                    </button>
                @endforeach

                <div class="row-span-2 flex flex-col items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg min-h-[100px]">
                    <button wire:click="selectYear({{ $selectedYear + 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <span class="text-2xl font-bold text-gray-800 dark:text-gray-200 my-1">{{ $selectedYear }}</span>
                    <button wire:click="selectYear({{ $selectedYear - 1 }})" class="p-1 text-gray-400 hover:text-primary-600 transition-colors">
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
                    >
                        {{ $name }}
                    </button>
                @endforeach
            </div>

            {{-- HAFTA BLOKLARI --}}
            <div class="grid grid-cols-4 gap-4">
                @foreach(range(1, 4) as $weekNum)
                    @php $week = $this->weeks->firstWhere('week_number', $weekNum); @endphp
                    <div class="min-h-[1px]">
                        @if($week)
                            <button
                                wire:click="selectWeek({{ $week->id }})"
                                @class([
                                    'w-full text-left p-5 rounded-xl border-2 transition-all duration-200 cursor-pointer relative overflow-hidden',
                                    'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg ring-1 ring-primary-300' => $selectedWeekId === $week->id,
                                    'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-primary-300 dark:hover:border-gray-500 hover:shadow-md' => $selectedWeekId !== $week->id,
                                ])
                            >
                                <span class="absolute top-2 right-3 text-4xl font-black opacity-10 select-none">{{ $weekNum }}</span>
                                <div @class([
                                    'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold mb-2',
                                    'bg-primary-600 text-white' => $selectedWeekId === $week->id,
                                    'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' => $selectedWeekId !== $week->id,
                                ])>
                                    {{ $weekNum }}
                                </div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $week->title }}</h3>
                                @if($week->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $week->description }}</p>
                                @endif
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($this->weeks->isEmpty())
                <div class="text-center py-10 text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Bu oy uchun haftalar topilmadi
                </div>
            @endif

            {{-- HAFTA TAFSILOTLARI --}}
            @if($selectedWeekId)
                <div class="grid grid-cols-5 gap-4">
                    <div class="col-span-1 space-y-2">
                        <div class="text-sm font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 text-center">Kunlar</div>
                        @foreach($this->days as $dayNum => $dayName)
                            <button
                                wire:click="selectDay({{ $dayNum }})"
                                @class([
                                    'w-full py-3 px-3 rounded-lg text-sm font-medium text-center transition-all duration-200 cursor-pointer',
                                    'bg-primary-600 text-white shadow-md' => $selectedDay === $dayNum,
                                    'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' => $selectedDay !== $dayNum,
                                ])
                            >
                                {{ $dayName }}
                            </button>
                        @endforeach
                    </div>

                    <div class="col-span-4 space-y-4">
                        {{-- STATISTIKA --}}
                        <div class="grid grid-cols-4 gap-3">
                            <div class="rounded-xl p-4 text-center border-2 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
                                <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $this->stats['completed'] }}</div>
                                <div class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mt-1">Bajarildi</div>
                            </div>
                            <div class="rounded-xl p-4 text-center border-2 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20">
                                <div class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $this->stats['not_completed'] }}</div>
                                <div class="text-xs font-medium text-rose-700 dark:text-rose-300 mt-1">Bajarilmadi</div>
                            </div>
                            <div class="rounded-xl p-4 text-center border-2 border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20">
                                <div class="text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $this->stats['under_review'] }}</div>
                                <div class="text-xs font-medium text-sky-700 dark:text-sky-300 mt-1">Tekshiruvda</div>
                            </div>
                            <div class="rounded-xl p-4 text-center border-2 border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20">
                                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $this->stats['rejected'] }}</div>
                                <div class="text-xs font-medium text-orange-700 dark:text-orange-300 mt-1">Rad etildi</div>
                            </div>
                        </div>

                        {{-- EXCEL YUKLAB OLISH --}}
                        @if($this->allGroups->isNotEmpty())
                            <div class="flex justify-end">
                                <button
                                    wire:click="exportExcel"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm cursor-pointer"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Excel yuklab olish
                                </button>
                            </div>
                        @endif

                        {{-- FAKULTET --}}
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

                        {{-- STATUS FILTERLARI --}}
                        <div class="grid grid-cols-5 gap-2">
                            <button wire:click="selectStatus('all')"
                                @class([
                                    'flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-all cursor-pointer',
                                    'bg-primary-600 text-white shadow-md' => $selectedStatus === 'all',
                                    'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' => $selectedStatus !== 'all',
                                ])>
                                Barchasi
                                <span @class([
                                    'inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold',
                                    'bg-white/20 text-white' => $selectedStatus === 'all',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-500' => $selectedStatus !== 'all',
                                ])>{{ $this->statusCounts['all'] }}</span>
                            </button>

                            @php
                                $statusButtons = [
                                    'completed' => ['label' => 'Bajarildi', 'color' => 'emerald'],
                                    'not_completed' => ['label' => 'Bajarilmadi', 'color' => 'rose'],
                                    'under_review' => ['label' => 'Tekshiruvda', 'color' => 'sky'],
                                    'rejected' => ['label' => 'Rad etildi', 'color' => 'orange'],
                                ];
                            @endphp

                            @foreach($statusButtons as $statusKey => $btn)
                                <button wire:click="selectStatus('{{ $statusKey }}')"
                                    @class([
                                        'flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-all cursor-pointer',
                                        "bg-{$btn['color']}-600 text-white shadow-md" => $selectedStatus === $statusKey,
                                        "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-{$btn['color']}-50 dark:hover:bg-gray-700" => $selectedStatus !== $statusKey,
                                    ])>
                                    {{ $btn['label'] }}
                                    <span @class([
                                        'inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full text-xs font-bold',
                                        'bg-white/20 text-white' => $selectedStatus === $statusKey,
                                        "bg-{$btn['color']}-100 dark:bg-{$btn['color']}-900/30 text-{$btn['color']}-700 dark:text-{$btn['color']}-400" => $selectedStatus !== $statusKey,
                                    ])>{{ $this->statusCounts[$statusKey] }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- GURUHLAR --}}
                        <div class="max-h-[520px] overflow-y-auto rounded-xl pr-1">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @forelse($this->groups as $group)
                                <a
                                    href="{{ route('filament.admin.resources.groups.edit', $group) }}"
                                    wire:navigate
                                    @class([
                                        'block p-4 rounded-xl border-l-4 transition-all duration-200 hover:shadow-lg group bg-white dark:bg-gray-800',
                                        'border-l-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30' => $group->aggregate_status === 'completed',
                                        'border-l-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30' => $group->aggregate_status === 'not_completed',
                                        'border-l-sky-500 hover:bg-sky-50 dark:hover:bg-sky-900/30' => $group->aggregate_status === 'under_review',
                                        'border-l-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/30' => $group->aggregate_status === 'rejected',
                                    ])
                                >
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 truncate group-hover:text-primary-600">
                                        {{ $group->name }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                        {{ $group->faculty?->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $group->curator?->name ?? '—' }}
                                    </p>
                                    <span @class([
                                        'inline-block mt-2 px-2.5 py-1 rounded-md text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' => $group->aggregate_status === 'completed',
                                        'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' => $group->aggregate_status === 'not_completed',
                                        'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300' => $group->aggregate_status === 'under_review',
                                        'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' => $group->aggregate_status === 'rejected',
                                    ])>
                                        @switch($group->aggregate_status)
                                            @case('completed') Bajarildi @break
                                            @case('not_completed') Bajarilmadi @break
                                            @case('under_review') Tekshiruvda @break
                                            @case('rejected') Rad etildi @break
                                        @endswitch
                                    </span>
                                </a>
                            @empty
                                <div class="col-span-full text-center py-12 text-gray-400 dark:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Guruhlar topilmadi
                                </div>
                            @endforelse
                        </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
