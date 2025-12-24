<?php

namespace App\Application;

use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\ValueObjects\Amount;

class DepositWallet
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {

    }
    public function execute(string $userID, Amount $amount): void
    {
        $user = $this->userRepository->findById($userID);
        if(!$user) {
            throw new UserNotFoundException('User not found');
        }
        $user->wallet()->credit($amount);
        $this->userRepository->saveBalance($user);


    }

}
