<?php

namespace Tests\Unit\Application;

use App\Application\CreateUser;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Exceptions\DocumentAlreadyExistsException;
use App\Domain\Exceptions\EmailAlreadyExistsException;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    private $repositoryMock;

    private CreateUser $useCase;

    protected function setUp(): void
    {
        $this->repositoryMock = \Mockery::mock(CustomerRepositoryInterface::class);
        $this->useCase = new CreateUser($this->repositoryMock);

    }

    public function test_should_create_a_user()
    {
        // GIVEN

        $user = Customer::create(
            fullname: 'Diego franca',
            document: Document::from('34067941064'),
            email: new Email('diego.tg.franca@gmail.com'),
            wallet: new Wallet(amount: new Amount(10000)),
            type: UserType::REGULAR
        );
        // WHEN
        $data = $user->toArray();
        $this->repositoryMock->shouldReceive('findByEmail')->once()->with($data['email'])->andReturnNull();
        $this->repositoryMock->shouldReceive('findByCpf')->once()->with($data['document'])->andReturnNull();
        $this->repositoryMock->shouldReceive('save')->once()->with($data)->andReturn($user);
        $response = $this->useCase->execute($data);
        // THEN
        $this->assertNull($response);
    }

    public function test_should_throw_exception_when_email_already_exists(): void
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
        $this->repositoryMock->shouldReceive('findByEmail')
            ->andReturn($data['email']);

        $this->repositoryMock->shouldReceive('findByCpf')
            ->andReturnNull();

        // THEN
        $this->expectException(EmailAlreadyExistsException::class);
        $this->expectExceptionMessage('Customer already exists');

        // WHEN
        $this->useCase->execute($data);
    }

    public function test_should_throw_exception_when_document_already_exists(): void
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
        $this->repositoryMock->shouldReceive('findByEmail')->andReturn(null);
        $this->repositoryMock->shouldReceive('findByCpf')
            ->andReturn(data: $data['document']);

        // THEN
        $this->expectException(DocumentAlreadyExistsException::class);

        // WHEN
        $this->useCase->execute($data);
    }
}
