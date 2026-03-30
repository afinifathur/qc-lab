@extends('layouts.modern', ['title' => 'Audit Logs Approvals'])

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs Approvals</h1>
    <p class="text-slate-500 mt-1">Catatan riwayat persetujuan dan pembatalan dokumen (Asia/Jakarta UTC+7).</p>
</div>

<div class="rounded-xl bg-white dark:bg-slate-900 shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Waktu (UTC+7)</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">ID Dokumen</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">IP Address</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Stamp ID</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($log->action === 'APPROVE')
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 px-2.5 py-0.5 text-xs font-bold uppercase">
                                APPROVED
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 px-2.5 py-0.5 text-xs font-bold uppercase">
                                REVOKED
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $log->user_name }}</span>
                            <span class="text-xs text-slate-500 uppercase">{{ $log->user_role }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-primary">#{{ $log->entity_id }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-mono">
                        {{ $log->ip }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 font-mono">
                        {{ $log->stamp_id }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-slate-500">Belum ada log audit.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
