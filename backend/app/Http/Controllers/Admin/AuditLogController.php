<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('admin:id,name,email');

        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }
        if ($entity = $request->query('entity')) {
            $query->where('entity', $entity);
        }
        if ($adminId = $request->query('admin')) {
            $query->where('admin_id', $adminId);
        }

        $logs = $query->orderByDesc('created_at')->limit(500)->get();

        return view('admin.audit-log.index', compact('logs'));
    }
}