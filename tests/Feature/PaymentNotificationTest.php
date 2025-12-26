<?php

namespace Tests\Feature;

use App\Events\MoneyTransferred;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testBasic()
    {
        // GIVEN
        Http::fake([
            'https://util.devi.tools/api/v1/notify' => Http::response([
                'success' => true
            ], 200),
        ]);
        $customer = \App\Models\Customer::factory()->create();

        $event = new MoneyTransferred(
            payeeId: $customer->id,
            amount: 15000
        );
        // WHEN
        event($event);

        // THEN
        Http::assertSent(function ($request) use($customer){
            return $request->url() === 'https://util.devi.tools/api/v1/notify'
                && $request->method() === 'POST'
                && $request['amount'] === 150.0
                && $request['email'] === $customer->email
                && $request['phone'] === '1234';
        });
    }
}
