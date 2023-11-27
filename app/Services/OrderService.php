<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        if (Order::where('order_id', $data['order_id'])->exists()) {
            // Log or handle duplicate order
            \Log::info('Duplicate order with order_id: ' . $data['order_id']);
            return;
        }

        // Check if the customer_email is associated with an affiliate
        $affiliate = Affiliate::where('email', $data['customer_email'])->first();

        // If the affiliate doesn't exist, create a new one
        if (!$affiliate) {
            $affiliate = $this->affiliateService->registerNewAffiliate(
                $data['customer_email'],
                $data['customer_name']
            );
        }

        // Create a new order and associate it with the affiliate
        $order = new Order([
            'order_id' => $data['order_id'],
            'subtotal_price' => $data['subtotal_price'],
            'merchant_domain' => $data['merchant_domain'],
            'discount_code' => $data['discount_code'],
        ]);

        // Associate the order with the affiliate
        $affiliate->orders()->save($order);

        // Log any commissions or additional processing logic
        \Log::info('Order processed for affiliate ' . $affiliate->id . ' with order_id: ' . $data['order_id']);
    }
}
