<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\View\View;

class ServerInfoController extends Controller
{
    /**
     * Display Minecraft Bedrock server connection information.
     */
    public function index(): View
    {
        $settings = SiteSetting::getAll();

        return view('server-info', compact('settings'));
    }
}
