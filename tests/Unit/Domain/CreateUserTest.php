<?php

namespace Tests\Unit\Domain;

use App\Application\CreateUser;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    private $repositoryMock;
    private CreateUser $useCase;

    public function setUp(): void
    {
        $this->repositoryMock = \Mockery::mock(UserRepositoryInterface::class);
        $this->useCase = new CreateUser($this->repositoryMock);

    }

    public function test_should_create_a_user()
    {
        //GIVEN
        $user  = User::create(
        fullname: 'Diego franca',
        document: new Cpf('34067941064'),
        email: new Email('diego.tg.franca@gmail.com'),
        wallet: new Wallet(amount: new Amount(10000)),
        type: UserType::REGULAR
    );
        //WHEN
        $this->repositoryMock->shouldReceive('findByEmail')->once()->andReturnNull();
        $this->repositoryMock->shouldReceive('findByCPF')->once()->andReturnNull();
        $this->repositoryMock->shouldReceive('save')->once()->andReturn($user);
        $response = $this->useCase->execute($user->toArray());
        //THEN
        $this->assertNull($response);
    }
}
