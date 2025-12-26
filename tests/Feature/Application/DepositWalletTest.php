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
            'balance' => 15000,
        ]);


    }

    public function test_should_throw_exception_when_customer_not_found(): void
    {
        // GIVEN
        $nonExistentId = (string) Uuid::generate();
        $amount = new Amount(5000);
        $useCase = app(DepositWallet::class);

        // THEN
        $this->expectException(\App\Domain\Exceptions\UserNotFoundException::class);
        $this->expectExceptionMessage('Customer not found');

        // WHEN
        $useCase->execute($nonExistentId, $amount);
    }
    public function test_should_return_throw_wallet_exception_when_deposit_fails(): void
    {
        // GIVEN
        $customer = \App\Models\Customer::factory()->create([
            'id'=>Uuid::generate(),
            'document' => '34067941064',
            'fullname'=>'Diego franca',
            'email'=>'teste@gmail.com',
            'password' => 'asdfadf'
        ]);

        $amount = new \App\Domain\ValueObjects\Amount(1000);

        // Simulamos que, ao tentar salvar o saldo, algo no domínio/repositório lança uma WalletException
        $this->instance(\App\Domain\Customer\CustomerRepositoryInterface::class, \Mockery::mock(\App\Domain\Customer\CustomerRepositoryInterface::class, function ($mock) use ($customer) {
            $mock->shouldReceive('findById')->with($customer->id)->andThrow(new \App\Domain\Exceptions\WalletException('Invalid wallet operation'));;
        })->makePartial());

        $useCase = app(\App\Application\DepositWallet::class);

        // THEN
        $this->expectException(\App\Domain\Exceptions\WalletException::class);
        $this->expectExceptionMessage('Error processing deposit');

        // WHEN
        $useCase->execute($customer->id, $amount);
    }


}
