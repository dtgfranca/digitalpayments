<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TransferMoneyTest extends TestCase
{
    public function testUserCanTransferMoney()
    {
        //GIVEN
        $payer = new User(
            id: 4,
            wallet: new Wallet(balance: 200.0),
            type: UserType::REGULAR
        );
        $payee = new User(
            id: 15,
            wallet: new Wallet(balance: 50.0),
            type: UserType::REGULAR
        );

        $useCase = new TransferMoney();

        //WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(100.0)
        );
        //THEN
        $this->assertEquals(100.0, $payer->wallet()->balance());
        $this->assertEquals(150.0, $payee->wallet()->balance());

    }
}
