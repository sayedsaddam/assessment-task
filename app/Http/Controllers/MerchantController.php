<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // TODO: Complete this method
        $data->validate([
            'name' => 'required',
            'email' => 'required',
            'api_key' => 'required',
        ]);
        Merchant::create($data->all());
        return redirect('merchant.index')->with('success', 'Merchant registered successfully!');
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method
        $data->validate([
            'name' => 'required',
            'email' => 'required',
            'api_key' => 'required'
        ]);
        $user->update($data->all());
        return redirect('merchant.index')->with('success', 'Merchant udpated successfully!');
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        // TODO: Complete this method
        $merchant = Merchant::where('email', $email)->first();
        return $merchant;
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        // TODO: Complete this method
        $unpaidOrders = $affiliate->orders()->where('paid', false)->get();
        foreach ($unpaidOrders as $order) {
            dispatch(new ProcessAffiliatePayout($order, $affiliate));
        }
    }
}
