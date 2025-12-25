<?php

namespace App\Application;

use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\ValueObjects\Amount;

class DepositWallet
{
    public function __construct(private readonly CustomerRepositoryInterface $userRepository)
    {

    }
    public function execute(string $userID, Amount $amount): void
    {
        $user = $this->userRepository->findById($userID);
        if(!$user) {
            throw new UserNotFoundException('Customer not found');
        }
        $user->wallet()->credit($amount);
        $this->userRepository->saveBalance($user);


    }

}
