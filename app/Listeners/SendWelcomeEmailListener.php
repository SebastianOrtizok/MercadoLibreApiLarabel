<?php

namespace App\Listeners;

use App\Http\Controllers\ControladorEmailBienvenida;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmailListener
{
    public function handle(Registered $event)
    {
        $controller = new ControladorEmailBienvenida();
        $controller->sendWelcomeEmail($event->user);
    }
}
