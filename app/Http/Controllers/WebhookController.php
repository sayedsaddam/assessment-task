<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Complete this method
        try {
            // Validate the incoming request data
            $request->validate([
                'order_id' => 'required|string',
                'subtotal_price' => 'required|numeric',
                'merchant_domain' => 'required|string',
                'discount_code' => 'nullable|string',
                'customer_email' => 'required|email',
                'customer_name' => 'required|string',
            ]);

            // Get the validated data
            $data = $request->only([
                'order_id',
                'subtotal_price',
                'merchant_domain',
                'discount_code',
                'customer_email',
                'customer_name',
            ]);

            // Pass the data to the processOrder method
            $this->yourService->processOrder($data);

            return response()->json(['message' => 'Order processed successfully'], 200);
        } catch (\Exception $e) {
            // Log or handle the exception
            \Log::error('Error processing order: ' . $e->getMessage());

            return response()->json(['error' => 'An error occurred while processing the order'], 500);
        }
    }
}
