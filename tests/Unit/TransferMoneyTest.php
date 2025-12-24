<?php

namespace Tests\Unit;

use App\Application\TranferMoney;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class TransferMoneyTest extends TestCase
{
    public function test_user_can_transfer_money()
    {
        // GIVEN
        $payer = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );


        $payee = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $useCase = new TranferMoney();

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(100.0, $payer->balance());
        $this->assertEquals(150.0, $payee->balance());

    }

    public function test_user_cannot_transfer_insuficient_funds()
    {
        // GIVEN
        $payer = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );


        $payee = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $useCase = new TranferMoney();

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(100.0, $payer->balance());
        $this->assertEquals(150.0, $payee->balance());

    }
}
