<?php

namespace Tests\Unit\Application;

use App\Application\TransferMoney;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\Exceptions\ProcessTransferFailedException;
use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class TransferMoneyTest extends TestCase
{
    private $eventDispacth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispacth = null;

    }

    public function test_user_can_transfer_money()
    {

        // GIVEN
        $payer = Customer::create(
            fullname: 'Diego franca',
            document: Document::from('80767437020'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('04623103021'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('commit')->once();
        });
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->once();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('saveBalance')->times(2);
        });
        $useCase = new TransferMoney($authorizerMock, $this->eventDispacth, $transferRepositoryMock, $transactionManager, $customerRepostitoryMock);

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(10000, $payer->wallet()->balance());
        $this->assertEquals(15000, $payee->wallet()->balance());

    }

    public function test_user_cannot_transfer_insuficient_funds()
    {
        // GIVEN
        $payer = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('04623103021'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(4000)),
            type: UserType::REGULAR
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('78008242094'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('rollback')->once();
        });
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new TransferMoney($authorizerMock, $this->eventDispacth, $transferRepositoryMock, $transactionManager, $customerRepostitoryMock);

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
        $payer = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('86272983000130'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(4000)),
            type: UserType::MERCHANT
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('78008242094'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class);
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class);

        $useCase = new TransferMoney($authorizerMock, $this->eventDispacth, $transferRepositoryMock, $transactionManager, $customerRepostitoryMock);

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
        $payer = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('69579045046'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(40000)),
            type: UserType::REGULAR
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('67651355024'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(5000)),
            type: UserType::REGULAR
        );
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class);
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class);
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(false);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class);

        // WHEN

        $useCase = new TransferMoney($authorizerMock, $this->eventDispacth, $transferRepositoryMock, $transactionManager, $customerRepostitoryMock);

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
        $payer = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('67651355024'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('22378312032'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );
        $transferRepositoryMock = \Mockery::mock(TransferRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->once();
        });
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('commit')->once();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('saveBalance')->times(2);
        });
        $dispatcher = \Mockery::mock(\Illuminate\Contracts\Events\Dispatcher::class, function ($mock) {
            $mock->shouldReceive('dispatch')->once();
        });
        $useCase = new TransferMoney($authorizerMock, $dispatcher, $transferRepositoryMock, $transactionManager, $customerRepostitoryMock);

        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );
        // THEN
        $this->assertEquals(10000, $payer->balance());
        $this->assertEquals(20000, $payee->balance());
    }

    public function test_should_make_rollback_when_transaction_failed(): void
    {
        // GIVEN
        $payer = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('22378312032'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(20000)),
            type: UserType::REGULAR
        );

        $payee = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );

        $transferMock = \Mockery::mock(TransferRepositoryInterface::class);
        $transactionManager = \Mockery::mock(TransactionMangerInterface::class, function ($mock) {
            $mock->shouldReceive('begin')->once();
            $mock->shouldReceive('rollback')->once();
        });
        $authorizerMock = \Mockery::mock(AuthorizerInterface::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(true);
        });
        $customerRepostitoryMock = \Mockery::mock(CustomerRepositoryInterface::class);
        $useCase = new TransferMoney($authorizerMock, $this->eventDispacth, $transferMock, $transactionManager, $customerRepostitoryMock);
        // THEN
        $this->expectException(ProcessTransferFailedException::class);
        $this->expectExceptionMessage('Error processing transfer');
        $this->assertEquals(20000, $payer->wallet()->balance());
        $this->assertEquals(10000, $payee->wallet()->balance());
        // WHEN
        $useCase->execute(
            payer: $payer,
            payee: $payee,
            amount: new Amount(10000)
        );

    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }
}
