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
                    Statistics by Endpoint
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Endpoint
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Total
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Success
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Failed
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Avg Response Time
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($statistics['requests_by_endpoint'] as $endpoint => $data)
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

