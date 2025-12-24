<?php

namespace Tests\Unit;

use App\Application\TranferMoney;
use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\Exceptions\TransferNotAllowedException;
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
            wallet: new Wallet(amount: new Amount(4000)),
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
        $this->expectException(InsuficientFundsException::class);
        $this->expectExceptionMessage('Insufficient funds');
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }

    public function test_user_cannot_transfer_when_was_merchant()
    {
        // GIVEN
        $payer = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(4000)),
            type: UserType::MERCHANT
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
        $this->expectException(TransferNotAllowedException::class);
        $this->expectExceptionMessage('Merchant profiles cannot make transfers, only receive them.');
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }
    public function test_should_return_exception_when_external_service_return_not_allowed()
    {
        // GIVEN
        $payer = new User(
            uuid: new Password(4),
            fullname: 'Diego franca',
            document: new Document('07634403694'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(40000)),
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
        $this->expectException(TransferNotAllowedException::class);
        $this->expectExceptionMessage('Transfer not allowed.');
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }
}
