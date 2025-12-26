<?php

namespace Tests\Feature\Application;

use App\Application\CreateUser;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Exceptions\DocumentAlreadyExistsException;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\ValueObjects\Uuid;
use App\Domain\Wallet\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CreateUser|\Illuminate\Foundation\Application|mixed|object
     */
    private mixed $userCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userCase = app(CreateUser::class);
    }

    public function test_should_create_a_user(): void
    {


        // GIVEN
        $user = Customer::create(
            fullname: 'Diego franca',
            document: Document::from('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(0)),
            type: UserType::REGULAR
        );

        // WHEN
        $data = $user->toArray();
        $data['password'] = 'teste';
        $this->userCase->execute($data);

        // THEN
        $this->assertDatabaseHas('customers', [
            'fullname' => 'Diego franca',
            'document' => '34067941064',
            'email' => 'diego.tg.franca@gmail.com',
        ]);
        $this->assertDatabaseHas('wallets', [
            'balance' => 0,
            'customer_id' => $data['id'],
        ]);
    }

    public function test_should_throw_exception_when_document_already_exists(): void
    {

        // GIVEN
        \App\Models\Customer::factory()->create([
            'id' => Uuid::generate(),
            'document' => '34067941064',
            'fullname' => 'Diego franca',
            'email' => 'teste@gmail.com',
            'password' => 'asdfadf',
        ]);
        $user = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(0)),
            type: UserType::REGULAR
        );
        $data = $user->toArray();
        // THEN
        $this->expectException(DocumentAlreadyExistsException::class);

        // WHEN
        $this->userCase->execute($data);
    }

    public function test_should_throw_generic_exception_when_repository_fails(): void
    {
        // GIVEN
        $user = Customer::create(
            fullname: 'Diego franca',
            document:Document::from('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );
        $data = $user->toArray();
        // Simula uma falha genérica no banco de dados ou repositório
        $this->instance(CustomerRepositoryInterface::class, Mockery::mock(CustomerRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('save')->andThrow(new \Exception('Database failure'));
        })->makePartial());

        // THEN
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating user');

        // WHEN
        $this->userCase->execute($data);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
