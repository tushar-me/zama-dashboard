<?php

namespace App;

use App\Models\Wallet;
use Illuminate\Support\Str;

trait HasWallet
{
    /**
     * Defines a polymorphic one-to-one relationship between the model and the Wallet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'owner');
    }

    /**
     * Initializes a wallet for the model if it doesn't already exist.
     * Ensures every model using this trait has an associated wallet.
     *
     * @return Wallet
     */
    public function initializeWallet()
    {
        if (!$this->wallet) {
            // Create a wallet with a default balance of 0
            return $this->wallet()->create(['balance' => 0]);
        }
        return $this->wallet;
    }

    /**
     * Deposits a specified amount into the model's wallet.
     * Creates a deposit transaction and updates the wallet balance.
     *
     * @param float $amount The amount to deposit.
     * @param string|null $description A description for the transaction (optional).
     * @return Wallet The updated wallet instance.
     */
    public function deposit(
        int|float $amount, 
        string $description = null, 
        string $payment_method = null, 
        string $reference_code = null,
        int $vat_amount = 0)
    {
        // Ensure the wallet exists
        $wallet = $this->initializeWallet();

        // Increase the wallet balance
        $wallet->balance += $amount;
        $wallet->save();

        // Log the deposit transaction
        $this->createTransaction($wallet, 'credit', $amount, $description, 'success', $payment_method, $reference_code, $vat_amount);

        return $wallet;
    }

    /**
     * Withdraws a specified amount from the model's wallet.
     * Creates a withdrawal transaction and updates the wallet balance.
     *
     * @param float $amount The amount to withdraw.
     * @param string|null $description A description for the transaction (optional).
     * @throws \Exception If the wallet has insufficient balance.
     * @return Wallet The updated wallet instance.
     */
    public function withdraw(int|float $amount, string $description = null, string $payment_method = null, string $reference_code = null)
    {
      
        // Ensure the wallet exists
        $wallet = $this->initializeWallet();

        // Check if the wallet has enough balance for the withdrawal
        // if ($wallet->balance < $amount) {
        //     throw new \Exception('Insufficient balance.');
        // }

        // Decrease the wallet balance
        $wallet->balance -= $amount;
        $wallet->save();

        // Log the withdrawal transaction
        $this->createTransaction($wallet, 'debit', $amount, $description, 'success', $payment_method, $reference_code);

        return $wallet;
    }

    /**
     * Creates a transaction record for the wallet.
     * Used internally by both deposit and withdraw methods.
     *
     * @param Wallet $wallet The wallet instance.
     * @param string $type The transaction type ('deposit' or 'withdraw').
     * @param float $amount The transaction amount.
     * @param string|null $description A description for the transaction (optional).
     * @return WalletTransaction The created transaction instance.
     */
    protected function createTransaction(
        Wallet $wallet, 
        string $type, 
        int|float $amount, 
        string $description = null, 
        string $status = null, string $payment_method = null,
        string $reference_code = null,
        int $vat_amount = 0
        )
    {
        // Create and return the transaction record
        return $wallet->transactions()->create([
            'trx_id' => substr(preg_replace('/\D/', '', crc32(uniqid(shopCode()))), 0, 6),
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'status' => $status ?? 'pending',
            'payment_method' => $payment_method,
            'owner_type' => get_class($this),
            'owner_id' => $this->id,
            'author_type' => request()->user() ? get_class(request()->user()) : 'App\Models\Admin',
            'author_id' => request()->user() ?  request()->user()->id : $this->id,
            'reference_code' => $reference_code,
            'vat_tax_amount' => $vat_amount
        ]);
    }
}