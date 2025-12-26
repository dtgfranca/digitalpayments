<?php

namespace App\Http\Controllers;

use App\Application\TransferMoney;
use App\Domain\Customer\Customer as CustomerDomain;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\ValueObjects\Amount;
use App\Domain\ValueObjects\Document;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserType;
use App\Domain\ValueObjects\Uuid;
use App\Domain\Wallet\Wallet;
use App\Http\Requests\TranferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferMoney $transferMoney,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * @OA\Post(
     *     path="/api/transfers",
     *     summary="Execute a transfer between customers",
     *     tags={"Transfers"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"payee_id","amount"},
     *
     *             @OA\Property(property="payee_id", type="string", example="uuid-of-payee"),
     *             @OA\Property(property="amount", type="integer", example=100)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Transfer executed successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Transfer executed successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function transfer(TranferRequest $request): JsonResponse
    {
        try {
            $payerId = Auth::guard('api')->id();
            $payeeId = $request->validated()['payee_id'];
            $amountValue = $request->validated()['amount'];

            $payerModel = $this->customerRepository->findById($payerId);
            $payeeModel = $this->customerRepository->findById($payeeId);

            if (! $payerModel || ! $payeeModel) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

            $payerDomain = CustomerDomain::restore(
                new Uuid($payerModel->id),
                $payerModel->fullname,
                Document::from($payerModel->document),
                new Email($payerModel->email),
                new Wallet(new Amount($payerModel->wallet->balance)),
                UserType::from($payerModel->type)
            );

            $payeeDomain = CustomerDomain::restore(
                new Uuid($payeeModel->id),
                $payeeModel->fullname,
                Document::from($payeeModel->document),
                new Email($payeeModel->email),
                new Wallet(new Amount($payeeModel->wallet->balance)),
                UserType::from($payeeModel->type)
            );

            $this->transferMoney->execute(
                $payerDomain,
                $payeeDomain,
                new Amount($amountValue)
            );

            return response()->json([
                'message' => 'Transfer executed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
