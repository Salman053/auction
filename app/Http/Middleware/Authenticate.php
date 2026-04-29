<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards): void
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $request->expectsJson() ? null : $this->redirectToGuard($guards),
        );
    }

    /**
     * @param  array<int, string>  $guards
     */
    private function redirectToGuard(array $guards): string
    {
        if (in_array('admin', $guards, true)) {
            return route('admin.login');
        }

        return route('login');
    }
}
