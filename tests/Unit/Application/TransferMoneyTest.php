<?php

namespace Tests\Unit\Application;

use App\Application\TranferMoney;
use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\Exceptions\ProcessTransferFailedException;
use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\Transfer;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class TransferMoneyTest extends TestCase
{
    public function test_user_can_transfer_money()
    {
        // GIVEN
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('80767437020'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('04623103021'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('commit')->once();
        });
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->once();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $notifiedMock = \Mockery::mock(NotifyerInterface::class, function ($mock) {
            $mock->shouldReceive('notify')->once()->andReturn();
        });
        $useCase = new TranferMoney($authorizerMock, $notifiedMock,$transferRepositoryMock,  $transactionManager);

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(100.0, $payer->wallet()->balance());
        $this->assertEquals(150.0, $payee->wallet()->balance());

    }

    public function test_user_cannot_transfer_insuficient_funds()
    {
        // GIVEN
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('04623103021'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(4000)),
            type: UserType::REGULAR
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('78008242094'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('rollback')->once();
        });
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $notifiedMock = \Mockery::mock(NotifyerInterface::class);
        $useCase = new TranferMoney($authorizerMock, $notifiedMock, $transferRepositoryMock, $transactionManager );

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
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('04623103021'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(4000)),
            type: UserType::MERCHANT
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('78008242094'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class);
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $notifiedMock = \Mockery::mock(NotifyerInterface::class);
        $useCase = new TranferMoney($authorizerMock, $notifiedMock, $transferRepositoryMock, $transactionManager);

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
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('69579045046'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(40000)),
            type: UserType::REGULAR
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('67651355024'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class);
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(false);
        });
        $notifiedMock = \Mockery::mock(NotifyerInterface::class);

        // WHEN

        $useCase = new TranferMoney($authorizerMock, $notifiedMock, $transferRepositoryMock, $transactionManager);

        // WHEN
        $this->expectException(TransferNotAllowedException::class);
        $this->expectExceptionMessage('Transfer not allowed.');
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }
    public function test_should_sent_notify_when_transaction_was_succed(): void
    {
        // GIVEN
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('67651355024'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('22378312032'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->once();
        });
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('commit')->once();
        });
        $notifiedMock = \Mockery::mock(NotifyerInterface::class, function ($mock) {
            $mock->shouldReceive('notify')->once()->andReturn();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $useCase = new TranferMoney($authorizerMock, $notifiedMock, $transferRepositoryMock, $transactionManager);

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(100.0, $payer->balance());
        $this->assertEquals(200.0, $payee->balance());
    }

    public function test_should_make_rollback_when_transaction_failed(): void
    {
        // GIVEN
        $payer = User::create(
            fullname: 'Diego franca',
            document: new Cpf('22378312032'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );


        $payee = User::create(
            fullname: 'Diego franca',
            document: new Cpf('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );
        $notifiedMock = \Mockery::mock(NotifyerInterface::class, function ($mock) {
            $mock->shouldReceive('notify')->andThrow(new \Exception('Notification service down'));
        });
        $transferMock =  \Mockery::mock(TransferRepositoryInterface::class);
        $transactionManager =  \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('rollback')->once();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $useCase = new TranferMoney($authorizerMock, $notifiedMock, $transferMock, $transactionManager);
        //THEN
        $this->expectException(ProcessTransferFailedException::class);
        $this->expectExceptionMessage('Error processing transfer');
        $this->assertEquals(200.0, $payer->wallet()->balance());
        $this->assertEquals(100.0, $payee->wallet()->balance());
        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }
    public function tearDown(): void
    {
        \Mockery::close();
    }

}
