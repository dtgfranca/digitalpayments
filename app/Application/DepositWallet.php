<?php

namespace App\Application;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Exceptions\WalletException;
use App\Domain\ValueObjects\Amount;
use App\Domain\Wallet\Wallet;

class DepositWallet
{
    public function __construct(private readonly CustomerRepositoryInterface $userRepository) {}

    public function execute(string $userID, Amount $amount): void
    {
        try {
            $user = $this->userRepository->findById($userID);
            if (! $user) {
                throw new UserNotFoundException('Customer not found');
            }
            $wallet = new Wallet(
                new Amount($user->wallet->balance)
            );
            $wallet->credit($amount);

            $this->userRepository->saveBalance($wallet->balance(), $userID);

        } catch (UserNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new WalletException('Error processing deposit');
        }

    }
}
