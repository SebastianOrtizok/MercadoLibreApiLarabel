<?php

namespace App\Listeners;

use App\Http\Controllers\ControladorEmailBienvenida;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailListener
{
    public function handle(Registered $event)
    {
        Log::info('Evento Registered recibido para: ' . $event->user->email);
        $controller = new ControladorEmailBienvenida();
        $controller->sendWelcomeEmail($event->user);
        Log::info('Correo de bienvenida enviado para: ' . $event->user->email);
    }
}
