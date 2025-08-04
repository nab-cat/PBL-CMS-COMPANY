<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use App\Notifications\WelcomeNotification;

class RegistrationResponse implements RegistrationResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Send welcome notification
        if ($user) {
            $user->notify(new WelcomeNotification());
        }

        return redirect()->intended('/');
    }
}