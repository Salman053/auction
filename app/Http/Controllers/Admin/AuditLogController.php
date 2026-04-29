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
        $search = trim((string) $request->string('q'));

        $query = AuditLog::query()->with('actor')->latest('created_at');

        if ($search !== '') {
            $query->where('event', 'like', '%'.$search.'%');
        }

        return view('admin.audit-logs.index', [
            'logs' => $query->paginate(25)->withQueryString(),
            'search' => $search,
        ]);
    }
}
