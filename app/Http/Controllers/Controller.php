<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use ValidatesRequests;

    protected $user;

    /**
     * Constructor
     */
    public function setup()
    {
        $this->user = Auth::user();
        Log::info('user', ['user in base controller' => $this->user]);
    }
}
