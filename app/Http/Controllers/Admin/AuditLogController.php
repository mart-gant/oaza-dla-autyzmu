<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        abort_if(! auth()->check() || auth()->user()->role !== 'admin', 403);

        $query = AuditLog::query()->with('user');

        if ($q = request('q')) {
            $query->where(fn ($w) => $w->where('action', 'like', "%{$q}%")->orWhere('meta', 'like', "%{$q}%"));
        }

        if ($actor = request('actor')) {
            $query->where('user_id', $actor);
        }

        if ($action = request('action')) {
            $query->where('action', $action);
        }

        if ($from = request('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = request('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->latest()->paginate(50)->appends(request()->query());
        return view('admin.audit_logs.index', compact('logs'));
    }

    public function export()
    {
        abort_if(! auth()->check() || auth()->user()->role !== 'admin', 403);

        $query = AuditLog::query();
        if ($q = request('q')) {
            $query->where(fn ($w) => $w->where('action', 'like', "%{$q}%")->orWhere('meta', 'like', "%{$q}%"));
        }
        if ($actor = request('actor')) {
            $query->where('user_id', $actor);
        }
        if ($action = request('action')) {
            $query->where('action', $action);
        }

        $rows = $query->latest()->get();

        $csv = "when,actor,action,target,meta\n";
        foreach ($rows as $r) {
            $when = $r->created_at->toDateTimeString();
            $actorName = optional($r->user)->email ?? 'system';
            $meta = json_encode($r->meta);
            $csv .= "\"{$when}\",\"{$actorName}\",\"{$r->action}\",\"{$r->target_type}#{$r->target_id}\",\"{$meta}\"\n";
        }

        $filename = 'audit_logs_'.now()->format('Ymd_His').'.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
