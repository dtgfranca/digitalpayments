<?php

namespace App\Http\Controllers;

use App\Application\DepositWallet;
use App\Http\Requests\WalletRequest;
use App\Domain\ValueObjects\Amount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function __construct(
        private readonly DepositWallet $depositWallet
    ) {}

    /**
     * @OA\Post(
     *     path="/api/wallet/deposit",
     *     summary="Make a deposit into the authenticated user's wallet",
     *     tags={"Wallet"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deposit successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Deposit successful")
     *         )
     *     ),
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
    public function deposit(WalletRequest $request): JsonResponse
    {
        try {
            $customer = Auth::guard('api')->user();
            $amount = new Amount($request->validated()['amount']);

            $this->depositWallet->execute($customer->id, $amount);

            return response()->json([
                'message' => 'Deposit successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
