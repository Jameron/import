<?php

namespace Jameron\Regulator\Listeners;

use Auth;
use Carbon\Carbon;

class UserEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function onUserLogin($event)
    {
        Auth::user()->last_login = Carbon::now();
        Auth::user()->save();
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event)
    {
        Auth::user()->last_logout = Carbon::now();
        Auth::user()->save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'Jameron\Regulator\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'Jameron\Regulator\Listeners\UserEventSubscriber@onUserLogout'
        );
    }
}
