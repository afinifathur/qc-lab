<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allowedEmails = ['kabagqc@peroniks.com', 'direktur@peroniks.com'];

        if (!in_array($user->email, $allowedEmails)) {
            abort(403, 'Akses ditolak. Hanya Kabag QC dan Direktur yang dapat melihat log audit.');
        }

        // Ambil log yang relevan dengan sample (approve/revoke)
        $logs = AuditLog::whereIn('action', ['APPROVE', 'REVOKE'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('audit-logs.index', compact('logs'));
    }
}
