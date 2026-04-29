<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserMultiplierRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $query = User::query()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('email', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        return view('admin.users.index', [
            'users' => $query->paginate(25)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function toggleSuspend(Request $request, User $user): RedirectResponse
    {
        $user->forceFill([
            'suspended_at' => $user->suspended_at ? null : now(),
        ])->save();

        return back()->with('success', 'User status updated successfully.');
    }

    public function updateMultiplier(UserMultiplierRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->forceFill([
            'bidding_multiplier_percent' => $validated['bidding_multiplier_percent'] ?? null,
        ])->save();

        return back()->with('success', 'Bidding multiplier updated successfully.');
    }
}
