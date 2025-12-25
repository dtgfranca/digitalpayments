<?php

namespace Tests\Feature\Application;

use App\Application\CreateUser;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->userCase = app(CreateUser::class);
    }

    public function test_should_create_a_user():void
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
        $data = $user->toArray();
        $response = $this->userCase->execute($data);

        //THEN
        $this->assertDatabaseHas('users', [
            'fullname' => 'Diego franca',
            'document' => '34067941064',
            'email' => 'diego.tg.franca@gmail.com'
        ]);
        $this->assertDatabaseHas('wallets', [
            'amount' => 10000,
            'user_id' => $response->id
        ]);





    }
}
