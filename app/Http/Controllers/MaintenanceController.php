<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display the maintenance page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Set maintenance status header
        return response()
            ->view('errors.maintenance')
            ->header('Retry-After', 3600) // Retry after 1 hour
            ->setStatusCode(503); // Service Unavailable
    }

    /**
     * Check maintenance status API endpoint.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return response()->json([
            'status' => 'maintenance',
            'message' => 'Sistem sedang dalam maintenance',
            'estimated_time' => '30 menit',
            'timestamp' => now()->toISOString()
        ], 503);
    }
}