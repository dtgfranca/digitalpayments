<?php

namespace App\Application;

use App\Application\DTO\TransferOutputDTO;
use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\Exceptions\ProcessTransferFailedException;
use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Transfer\Transfer;
use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Domain\User\User;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\UserType;

class TransferMoney
{
    public function __construct(
        private readonly AuthorizerInterface $authorizer,
        private readonly NotifyerInterface $notifyer,
        private readonly TransferRepositoryInterface $transferRepository,
        private readonly TransactionMangerInterface $transactionManger
    )
    {

    }

    public function execute(User $payer, User $payee, Amount $amount): void
    {
        if(!$payer->canSendMoney()) {
            throw new TransferNotAllowedException('Merchant profiles cannot make transfers, only receive them.');
        }
        if(!$this->authorizer->authorize()){
            throw new TransferNotAllowedException('Transfer not allowed.');
        }
        $payerMemento = $payer->wallet()->createMemento();
        $payeeMemento = $payee->wallet()->createMemento();
        $this->transactionManger->begin();
        try{
            $transfer = Transfer::create(
                payer: $payer,
                payee: $payee,
                amount: $amount,
            );
            $transfer->commit();
            $this->transferRepository->save(TransferOutputDTO::fromTransfer($transfer)->toArray());
            $this->notifyer->notify($payee);
            $this->transactionManger->commit();
        }catch (InsuficientFundsException $e){
            $this->transactionManger->rollback();
            throw $e;
        }
        catch (\Throwable $e){
            $this->transactionManger->rollback();
            $payer->wallet()->restore($payerMemento);
            $payee->wallet()->restore($payeeMemento);
            throw new ProcessTransferFailedException('Error processing transfer');
        }


    }
}
