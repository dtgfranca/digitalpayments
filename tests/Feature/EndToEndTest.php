<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Wallet;
use App\Domain\ValueObjects\Uuid;

class EndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_flow()
    {
        // 1. Create a common customer
        $response = $this->postJson('/api/customers', [
            'fullname' => 'John Doe',
            'email' => 'john@example.com',
            'document' => '74494360007',
            'password' => 'password123',
            'type' => 'REGULAR',
            'balance' => 1000,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Customer created successfully']);

        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
        $john = Customer::where('email', 'john@example.com')->first();

        // 2. Create a merchant customer
        $response = $this->postJson('/api/customers', [
            'fullname' => 'Merchant Shop',
            'email' => 'shop@example.com',
            'document' => '36835116056',
            'password' => 'password123',
            'type' => 'MERCHANT',
            'balance' => 0,
        ]);

        $response->assertStatus(201);
        $merchant = Customer::where('email', 'shop@example.com')->first();

        // 3. Login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $token = $response->json('access_token');

        // 4. Deposit
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/wallet/deposit', [
                'amount' => 500,
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Deposit successful']);

        $this->assertEquals(1500, $john->wallet->fresh()->balance);

        // 5. Transfer
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/transfers', [
                'payee_id' => $merchant->id,
                'amount' => 300,
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transfer executed successfully']);

        $this->assertEquals(1200, $john->wallet->fresh()->balance);
        $this->assertEquals(300, $merchant->wallet->fresh()->balance);
    }

    public function test_merchant_cannot_transfer()
    {
        // Create merchant
        $this->postJson('/api/customers', [
            'fullname' => 'Merchant Shop',
            'email' => 'shop@example.com',
            'document' => '00433866012',
            'password' => 'password123',
            'type' => 'MERCHANT',
            'balance' => 1000,
        ]);
        $merchant = Customer::where('email', 'shop@example.com')->first();

        // Login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'shop@example.com',
            'password' => 'password123',
        ]);
        $token = $response->json('access_token');

        // Try transfer
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/transfers', [
                'payee_id' => Uuid::generate()->value(), // random payee
                'amount' => 100,
            ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('Merchant profiles cannot make transfers', $response->json('message'));
    }

    public function test_insufficient_funds()
    {
        // Create customer
        $this->postJson('/api/customers', [
            'fullname' => 'John Doe',
            'email' => 'john@example.com',
            'document' => '63574788061',
            'password' => 'password123',
            'type' => 'REGULAR',
            'balance' => 100,
        ]);
        $john = Customer::where('email', 'john@example.com')->first();

        // Create payee
        $this->postJson('/api/customers', [
            'fullname' => 'Jane Doe',
            'email' => 'jane@example.com',
            'document' => '74494360007',
            'password' => 'password123',
            'type' => 'REGULAR',
            'balance' => 0,
        ]);
        $jane = Customer::where('email', 'jane@example.com')->first();

        // Login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        $token = $response->json('access_token');

        // Try transfer more than balance
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/transfers', [
                'payee_id' => $jane->id,
                'amount' => 200,
            ]);

        $response->assertStatus(400);
    }
}
