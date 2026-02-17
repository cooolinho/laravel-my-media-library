<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Overall Statistics --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">
                        {{ number_format($statistics['total_requests'] ?? 0) }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Total Requests
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">
                        {{ number_format($statistics['success_rate'] ?? 0, 2) }}%
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Success Rate
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-info-600">
                        {{ number_format($statistics['cache_hit_rate'] ?? 0, 2) }}%
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Cache Hit Rate
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-warning-600">
                        {{ number_format($statistics['average_response_time'] ?? 0, 0) }} ms
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Avg Response Time
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Request Status Distribution --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ number_format($statistics['successful_requests'] ?? 0) }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Successful Requests
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ number_format($statistics['failed_requests'] ?? 0) }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Failed Requests
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ number_format($statistics['cached_requests'] ?? 0) }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Cached Requests
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Endpoint Statistics --}}
        @if(!empty($statistics['requests_by_endpoint']))
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <span>Statistics by Endpoint</span>
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            {{ number_format(count($statistics['requests_by_endpoint'])) }} total endpoints
                        </span>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Endpoint
                            </th>
                            <th wire:click="sortEndpoints('total')"
                                class="cursor-pointer px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <div class="flex items-center justify-center gap-1">
                                    Total
                                    @if($sortBy === 'total')
                                        <x-filament::icon
                                                :icon="$sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                                                class="h-4 w-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortEndpoints('successful')"
                                class="cursor-pointer px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <div class="flex items-center justify-center gap-1">
                                    Success
                                    @if($sortBy === 'successful')
                                        <x-filament::icon
                                                :icon="$sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                                                class="h-4 w-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortEndpoints('failed')"
                                class="cursor-pointer px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <div class="flex items-center justify-center gap-1">
                                    Failed
                                    @if($sortBy === 'failed')
                                        <x-filament::icon
                                                :icon="$sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                                                class="h-4 w-4"
                                        />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortEndpoints('avg_response_time')"
                                class="cursor-pointer px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <div class="flex items-center justify-center gap-1">
                                    Avg Response Time
                                    @if($sortBy === 'avg_response_time')
                                        <x-filament::icon
                                                :icon="$sortDirection === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                                                class="h-4 w-4"
                                        />
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getPaginatedEndpoints() as $endpoint => $data)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 font-mono text-sm text-gray-900 dark:text-gray-100">
                                    {{ $endpoint }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($data['total']) }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                        <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-medium text-success-800 dark:bg-success-800 dark:text-success-100">
                                            {{ number_format($data['successful']) }}
                                        </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                        <span class="inline-flex items-center rounded-full bg-danger-100 px-2.5 py-0.5 text-xs font-medium text-danger-800 dark:bg-danger-800 dark:text-danger-100">
                                            {{ number_format($data['failed']) }}
                                        </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($data['avg_response_time'] < 100)
                                                bg-success-100 text-success-800 dark:bg-success-800 dark:text-success-100
                                            @elseif($data['avg_response_time'] < 500)
                                                bg-warning-100 text-warning-800 dark:bg-warning-800 dark:text-warning-100
                                            @else
                                                bg-danger-100 text-danger-800 dark:bg-danger-800 dark:text-danger-100
                                            @endif
                                        ">
                                            {{ number_format($data['avg_response_time'], 2) }} ms
                                        </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($this->getTotalPages() > 1)
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                        <div class="flex flex-1 justify-between sm:hidden">
                            <button
                                    wire:click="previousPage"
                                    @if($currentPage === 1) disabled @endif
                                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Previous
                            </button>
                            <button
                                    wire:click="nextPage"
                                    @if($currentPage === $this->getTotalPages()) disabled @endif
                                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Next
                            </button>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ (($currentPage - 1) * $endpointsPerPage) + 1 }}</span>
                                    to
                                    <span class="font-medium">{{ min($currentPage * $endpointsPerPage, count($statistics['requests_by_endpoint'])) }}</span>
                                    of
                                    <span class="font-medium">{{ number_format(count($statistics['requests_by_endpoint'])) }}</span>
                                    endpoints
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                     aria-label="Pagination">
                                    <button
                                            wire:click="previousPage"
                                            @if($currentPage === 1) disabled @endif
                                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 dark:ring-gray-600 dark:hover:bg-gray-700"
                                    >
                                        <span class="sr-only">Previous</span>
                                        <x-filament::icon
                                                icon="heroicon-m-chevron-left"
                                                class="h-5 w-5"
                                        />
                                    </button>

                                    @php
                                        $totalPages = $this->getTotalPages();
                                        $start = max(1, $currentPage - 2);
                                        $end = min($totalPages, $currentPage + 2);
                                    @endphp

                                    @if($start > 1)
                                        <button
                                                wire:click="goToPage(1)"
                                                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700"
                                        >
                                            1
                                        </button>
                                        @if($start > 2)
                                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 dark:text-gray-300 dark:ring-gray-600">...</span>
                                        @endif
                                    @endif

                                    @for($i = $start; $i <= $end; $i++)
                                        <button
                                                wire:click="goToPage({{ $i }})"
                                                @class([
                                                    'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 dark:ring-gray-600',
                                                    'z-10 bg-primary-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600' => $i === $currentPage,
                                                    'text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-gray-700' => $i !== $currentPage,
                                                ])
                                        >
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    @if($end < $totalPages)
                                        @if($end < $totalPages - 1)
                                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 dark:text-gray-300 dark:ring-gray-600">...</span>
                                        @endif
                                        <button
                                                wire:click="goToPage({{ $totalPages }})"
                                                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700"
                                        >
                                            {{ $totalPages }}
                                        </button>
                                    @endif

                                    <button
                                            wire:click="nextPage"
                                            @if($currentPage === $totalPages) disabled @endif
                                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 dark:ring-gray-600 dark:hover:bg-gray-700"
                                    >
                                        <span class="sr-only">Next</span>
                                        <x-filament::icon
                                                icon="heroicon-m-chevron-right"
                                                class="h-5 w-5"
                                        />
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center text-gray-500 dark:text-gray-400">
                    No statistics available for the selected period.
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

