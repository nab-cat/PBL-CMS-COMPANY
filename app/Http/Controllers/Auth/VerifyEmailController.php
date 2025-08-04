<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        Log::info('VerifyEmailController hit', ['user' => $request->user()]);
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home', ['verified' => 1]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            // Send exactly one database notification
            $request->user()->notify(new \App\Notifications\EmailVerifiedNotification());
        }

        // Redirect to home so Inertia share middleware will fetch notifications
        return redirect()->route('home', ['verified' => 1]);
    }
}
