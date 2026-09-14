<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(
        Request $request
    ): View {
        $query = AuditLog::with('user');

        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->user_id
            );
        }

        if ($request->filled('action')) {
            $query->where(
                'action',
                'like',
                '%' .
                $request->action .
                '%'
            );
        }

        if ($request->filled('date')) {
            $query->whereDate(
                'created_at',
                $request->date
            );
        }

        $logs = $query
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')
            ->get();

        return view(
            'audit.index',
            compact(
                'logs',
                'users'
            )
        );
    }
}
