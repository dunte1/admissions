@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('labels.analytics') ?? 'Analytics' }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Monthly Applications</h2>
            <div class="space-y-4">
                @forelse($monthlyStats ?? [] as $stat)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ date('F', mktime(0, 0, 0, $stat->month, 1)) }}</span>
                    <span class="font-semibold">{{ $stat->total }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center">No data available</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Status Distribution</h2>
            <div class="space-y-4">
                @forelse($statusDistribution ?? [] as $status)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $status->status) }}</span>
                        <span class="font-semibold">{{ $status->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($status->count / max($statusDistribution->sum('count'), 1)) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">Top Programs by Approved Applications</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($topPrograms ?? [] as $program)
                    <tr>
                        <td class="px-4 py-2">{{ $program->name }}</td>
                        <td class="px-4 py-2 font-semibold">{{ $program->applications_count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-center text-gray-500">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
