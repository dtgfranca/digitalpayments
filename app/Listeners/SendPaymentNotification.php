<?php

namespace App\Listeners;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Transfer\NotifyerInterface;
use App\Events\MoneyTransferred;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentNotification implements ShouldQueue
{
    public $queue = 'notifications-money-transferred';

    public $delay = 10;

    public $tries = 3;

    /**
     * Create the event listener.
     */
    public function __construct(private readonly NotifyerInterface $notifier, private readonly CustomerRepositoryInterface $customerRepository) {}

    /**
     * Handle the event.
     */
    public function handle(MoneyTransferred $event): void
    {
        $customer = $this->customerRepository->findById($event->payeeId);

        $this->notifier->notify($customer->email, '1234', $event->amount);
    }
}
