<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\BedrockServerStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ServerInfoController extends Controller
{
    public function __construct(
        protected BedrockServerStatusService $statusService
    ) {}

    /**
     * Display Minecraft Bedrock server connection information.
     */
    public function index(): View
    {
        $settings = SiteSetting::getAll();
        $serverStatus = $this->statusService->getStatus();

        return view('server-info', compact('settings', 'serverStatus'));
    }

    /**
     * API endpoint for live server status polling.
     */
    public function statusJson(): JsonResponse
    {
        return response()->json($this->statusService->getStatus());
    }
}
