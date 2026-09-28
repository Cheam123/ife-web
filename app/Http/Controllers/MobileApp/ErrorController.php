<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use App\Models\Error;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Store error log from mobile app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'page_name' => 'nullable|string|max:255',
                'error_message' => 'nullable|string',
                'input' => 'nullable|string',
            ]);

            $error = Error::create($validated);

            return $this->response_success($error, 'Error logged successfully');

        } catch (\Exception $e) {
            // Log backend errors too
            \Log::error('Error logging failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return $this->response_failed('Failed to log error', $e->getMessage());
        }
    }
}
