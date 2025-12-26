<?php

namespace App\Application;

use App\Application\DTO\TransferOutputDTO;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Exceptions\InsuficientFundsException;
use App\Domain\Exceptions\ProcessTransferFailedException;
use App\Domain\Exceptions\TransferNotAllowedException;
use App\Domain\Transfer\AuthorizerInterface;
use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Transfer\Transfer;
use App\Domain\Transfer\TransactionMangerInterface;
use App\Domain\Transfer\TransferRepositoryInterface;
use App\Domain\Customer\Customer;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\UserType;
use App\Events\MoneyTransferred;

class TransferMoney
{
    public function __construct(
        private readonly AuthorizerInterface $authorizer,
        private readonly NotifyerInterface $notifyer,
        private readonly TransferRepositoryInterface $transferRepository,
        private readonly TransactionMangerInterface $transactionManger,
        private readonly CustomerRepositoryInterface $customerRepository,
    )
    {

    }

    public function execute(Customer $payer, Customer $payee, Amount $amount): void
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
            $this->customerRepository->saveBalance($payer->wallet()->balance(), $payer->getUuid()->value());
            $this->customerRepository->saveBalance($payee->wallet()->balance(), $payee->getUuid()->value());
            event(new MoneyTransferred(
                payeeId: $payee->getUuid()->value(),
                amount: $amount->value()
            ));
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
