<?php

namespace Tests\Unit\Application;

use App\Application\DepositWallet;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class DepositWalletTest extends TestCase
{
    public function test_should_deposit_money_in_wallet()
    {
        $this->markTestSkipped('Not implemented yet');
        // GIVEN
        $uuid = Uuid::generate();
        $userMock = \Mockery::mock(Customer::class);
        $repositoryMock = \Mockery::mock(CustomerRepositoryInterface::class, function ($mock)  use($userMock, $uuid){
            $mock->shouldReceive('findById')->once()
                ->with($uuid)
                ->andReturn($userMock);
        });
        $amount = new Amount(5000); // R$ 50,00

        $useCase = new DepositWallet($repositoryMock);
        //WHEN
        $response = $useCase->execute($uuid, $amount);
        $this->assertNull($response);


    }
    public function tearDown(): void
    {
        \Mockery::close();
    }
}
