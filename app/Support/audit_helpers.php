<?php

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

if (! function_exists('audit_log')) {
    /**
     * Catat audit log dan kembalikan stamp_id (UUID).
     *
     * @param string $action  contoh: 'preview_pdf', 'download_pdf', 'print_pdf', 'store_sample', ...
     * @param array  $ctx     ['entity'=>$model, 'meta'=>[], 'stamp_id'=>string|null, 'entity_type'=>..., 'entity_id'=>...]
     * @param Request|null $req
     * @return string $stampId
     */
    function audit_log(string $action, array $ctx = [], ?Request $req = null): string
    {
        $req   = $req ?: request();
        $user  = $req?->user();
        $stamp = $ctx['stamp_id'] ?? (string) Str::uuid();

        $entity = $ctx['entity'] ?? null;
        $etype  = $entity ? get_class($entity) : ($ctx['entity_type'] ?? null);
        $eid    = $entity?->getKey() ?? ($ctx['entity_id'] ?? null);

        // role: jika pakai spatie/permission
        $roleNames = null;
        if (method_exists($user, 'roles')) {
            $roleNames = $user?->roles()->pluck('name')->implode('|');
        }

        AuditLog::create([
            'stamp_id'    => $stamp,
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'user_role'   => $roleNames,
            'action'      => $action,
            'entity_type' => $etype,
            'entity_id'   => $eid,
            'route'       => optional($req->route())->getName(),
            'method'      => $req->method(),
            'ip'          => $req->ip(),
            'user_agent'  => $req->userAgent(),
            'meta'        => $ctx['meta'] ?? null,
        ]);

        return $stamp;
    }
}
