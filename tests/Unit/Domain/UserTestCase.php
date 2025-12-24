<?php

namespace Tests\Unit\Domain\User;

use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_regular_user_can_send_money(): void
    {
        // GIVEN
        $user = new User(
            uuid: new Password(4),
            fullname: 'User Regular',
            document: new Document('07634403694'),
            email: new Email('regular@example.com'),
            wallet: new Wallet(new Amount(1000)),
            type: UserType::REGULAR
        );

        // THEN
        $this->assertTrue($user->canSendMoney());
        $this->assertEquals(UserType::REGULAR->value, $user->getTypeUser());
    }

    public function test_merchant_user_cannot_send_money(): void
    {
        // GIVEN
        $user = new User(
            uuid: new Password(4),
            fullname: 'User Merchant',
            document: new Document('07634403694'),
            email: new Email('merchant@example.com'),
            wallet: new Wallet(new Amount(1000)),
            type: UserType::MERCHANT
        );

        // THEN
        $this->assertFalse($user->canSendMoney());
        $this->assertEquals(UserType::MERCHANT->value, $user->getTypeUser());
    }

    public function test_user_should_return_correct_balance_from_wallet(): void
    {
        // GIVEN
        $initialAmount = 5000; // 50.00
        $wallet = new Wallet(new Amount($initialAmount));
        $user = new User(
            uuid: new Password(4),
            fullname: 'User Test',
            document: new Document('07634403694'),
            email: new Email('test@example.com'),
            wallet: $wallet,
            type: UserType::REGULAR
        );

        // THEN
        $this->assertEquals(50.0, $user->balance());
    }

    public function test_user_wallet_interactions(): void
    {
         // GIVEN
         $user = new User(
            uuid: new Password(4),
            fullname: 'User Test',
            document: new Document('07634403694'),
            email: new Email('test@example.com'),
            wallet: new Wallet(new Amount(10000)), // 100.00
            type: UserType::REGULAR
        );

        // WHEN
        $user->wallet()->debit(new Amount(3000)); // - 30.00

        // THEN
        $this->assertEquals(70.0, $user->balance());
    }
}
