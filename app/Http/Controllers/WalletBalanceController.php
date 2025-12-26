<?php

namespace App\Http\Controllers;

use App\Application\GetWalletBalance;
use Illuminate\Support\Facades\Auth;

class WalletBalanceController extends Controller
{
    public function __construct(
        private readonly GetWalletBalance $getWalletBalance
    ) {}

    /**
     * @OA\Get(
     *     path="/api/wallets/balance",
     *     security={{"bearerAuth":{}}},
     *     tags={"Wallet"},
     *     summary="Get user wallet balance",
     *     description="Returns the current balance of the user's wallet. This is a read-only operation (Query) and does not modify any system state.",
     *     operationId="getWalletBalance",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Wallet balance retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="user_id",
     *                 type="string",
     *                 example="c1a9b2e4-7f12-4a88-9c01-9bdeff123456"
     *             ),
     *             @OA\Property(
     *                 property="balance",
     *                 type="number",
     *                 format="float",
     *                 description="Available wallet balance",
     *                 example=150.75
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Wallet not found for the given user"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated user"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function __invoke()
    {
        $balance = $this->getWalletBalance->execute(Auth::guard('api')->id());

        return response()->json([
            'user_id' => $balance->customer_id,
            'balance' => $balance->balance / 100,
        ]);
    }
}
