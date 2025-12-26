<?php

namespace Tests\Unit\Domain;

use App\Domain\Customer\Customer;
use App\Domain\Exceptions\InvalidDocumentException;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Cpf;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\Wallet\Wallet;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function test_should_create_valid_email(): void
    {
        $emailStr = 'diego.tg.franca@gmail.com';
        $email = new Email($emailStr);
        $this->assertEquals($emailStr, $email->value());
    }

    public function test_should_throw_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('email-invalido');
    }

    public function test_regular_user_can_send_money(): void
    {

        // GIVEN
        $user = Customer::create(
            fullname: 'Customer Regular',
            document: Document::from('07634493694'),
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
        $user = Customer::create(

            fullname: 'Customer Merchant',
            document: Document::from('98000832000102'),
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
        $user = Customer::create(
            fullname: 'Customer Test',
            document: Document::from('07634493694'),
            email: new Email('test@example.com'),
            wallet: $wallet,
            type: UserType::REGULAR
        );

        // THEN
        $this->assertEquals(5000, $user->balance());
    }

    public function test_should_accept_a_valid_cpf(): void
    {
        // GIVEN
        $validCpf = '07634493694';

        // WHEN
        $document = Document::from($validCpf);

        // THEN
        $this->assertEquals($validCpf, $document->value());
    }

    public function test_should_accept_cpf_with_formatting_and_sanitize(): void
    {
        // GIVEN
        $formattedCpf = '076.344.936-94';
        $expectedCpf = '07634493694';

        // WHEN
        $document = Document::from($formattedCpf);

        // THEN
        $this->assertEquals($expectedCpf, $document->value());
    }

    public function test_should_throw_exception_for_invalid_cpf_digits(): void
    {
        // THEN
        $this->expectException(InvalidDocumentException::class);
        $this->expectExceptionMessage('Document Invalid');

        // WHEN (CPF com último dígito errado)
        Document::from('07634403695');
    }
}
