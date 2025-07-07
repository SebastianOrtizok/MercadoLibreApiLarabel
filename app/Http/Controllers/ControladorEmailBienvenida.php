<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class ControladorEmailBienvenida extends Controller
{
    public function sendWelcomeEmail($user)
    {
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}
