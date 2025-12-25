<?php

namespace Tests\Feature\Application;

use App\Application\DepositWallet;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Uuid;
use App\Models\Customer;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositWalletTest extends TestCase
{
    use RefreshDatabase;
    public function test_should_deposit_money_in_wallet()
    {

        // GIVEN
        $customer = Customer::factory()->create([
            'id' => (string) Uuid::generate(),
            'document' => '34067941064',
            'fullname' => 'Diego franca',
            'email' => 'teste@gmail.com',
            'password' => 'asdfadf',
        ]);

        Wallet::create([
            'id' => (string) Uuid::generate(),
            'customer_id' => $customer->id,
            'balance' => 10000,
        ]);

        $amount = new Amount(5000); // R$ 50,00

        /** @var DepositWallet $useCase */
        $useCase = app(DepositWallet::class);

        // WHEN
        $useCase->execute($customer->id, $amount);

        // THEN
        $this->assertDatabaseHas('wallets', [
            'customer_id' => $customer->id,
            'balance' => 150,
        ]);


    }
}
