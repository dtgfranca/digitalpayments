<?php

namespace Tests\Feature\Application;

use App\Application\CreateUser;
use App\Application\TransferMoney;
use App\Domain\Customer\Customer;
use App\Domain\Exceptions\ProcessTransferFailedException;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Events\MoneyTransferred;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransferMoneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Event::fake();
    }

    public function test_should_execute_transfer_successfully(): void
    {
        // WHEN

        $payer = $this->createCustomer(
            name: 'Diego franca',
            document: '34067941064',
            email: 'teste@mail.com', amount: Amount::toCents(1000.00)

        );

        $payee = $this->createCustomer(
            name: 'Leo franca',
            document: '78008242094',
            email: 'teste1@mail.com', amount: 0
        );
        Http::fake([
            '*/authorize' => Http::response(['status' => 'success', 'data' => ['authorization' => true]], 200),
        ]);
        $transferAmount = Amount::toCents(150.50);
        $amount = new Amount($transferAmount);
        $service = app(TransferMoney::class);

        // WHEN
        $service->execute($payer, $payee, $amount);
        $balancePayer = \App\Models\Customer::where('id', $payer->toArray()['id'])->first();
        $balancePayee = \App\Models\Customer::where('id', $payee->toArray()['id'])->first();

        // THEN

        $this->assertEquals(849.50, (new Amount($balancePayer->wallet->balance))->toFloat());
        $this->assertEquals(150.50, (new Amount($balancePayee->wallet->balance))->toFloat());
        \Event::assertDispatched(MoneyTransferred::class);
        $this->assertDatabaseHas('transfers', [
            'payer_id' => $payer->getUuid(),
            'payee_id' => $payee->getUuid(),
            'amount' => $transferAmount,
        ]);
    }

    public function test_should_rollback_transaction_on_failure(): void
    {
        // WHEN
        $payer = $this->createCustomer(
            name: 'Diego franca',
            document: '34067941064',
            email: 'teste@mail.com', amount: Amount::toCents(1000.00)

        );

        $payee = $this->createCustomer(
            name: 'Leo franca',
            document: '78008242094',
            email: 'teste1@mail.com', amount: 0
        );

        Http::fake([
            '*/authorize' => Http::response(['status' => 'success', 'data' => ['authorization' => true]], 200),
        ]);
        $this->instance(TransferRepositoryInterface::class, \Mockery::mock(TransferRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->andThrow(new \Exception('Error processing transfer'));
        }));
        $service = app(TransferMoney::class);

        // THEN
        $this->expectException(ProcessTransferFailedException::class);

        try {
            $service->execute($payer, $payee, new Amount(100));
        } finally {
            // Verifica se os saldos permanecem como anterior (Rollback)
            $balancePayer = \App\Models\Customer::where('id', $payer->toArray()['id'])->first();
            $balancePayee = \App\Models\Customer::where('id', $payee->toArray()['id'])->first();

            $this->assertEquals(1000.0, (new Amount($balancePayer->wallet->balance))->toFloat());
            $this->assertEquals(0.0, (new Amount($balancePayee->wallet->balance))->toFloat());

            $this->assertDatabaseCount('transfers', 0);
        }
    }

    /**
     * @return array
     *
     * @throws \App\Domain\Exceptions\DocumentAlreadyExistsException
     * @throws \App\Domain\Exceptions\EmailAlreadyExistsException
     */
    public function createCustomer(
        string $name,
        string $document,
        string $email,
        int $amount
    ): Customer {
        $customerPayer = Customer::create(
            fullname: $name,
            document: new Cpf($document),
            email: new Email($email),
            wallet: new \App\Domain\Wallet\Wallet(new Amount($amount)),
            type: UserType::REGULAR
        );
        $dataPayer = $customerPayer->toArray();
        $dataPayer['password'] = 123;
        app(CreateUser::class)->execute($dataPayer);

        return $customerPayer;
    }
}
