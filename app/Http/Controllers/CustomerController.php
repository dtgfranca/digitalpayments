<?php

namespace App\Http\Controllers;

use App\Application\CreateUser;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Domain\ValueObjects\Uuid;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CreateUser $createUser
    ) {}

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Create a new customer",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fullname","email","document","password","type","balance"},
     *             @OA\Property(property="fullname", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="document", type="string", example="12345678900"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="type", type="string", enum={"common", "merchant"}, example="common"),
     *             @OA\Property(property="balance", type="integer", example=1000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['id'] = Uuid::generate();
            $data['password'] = bcrypt($data['password']);

            $this->createUser->execute($data);

            return response()->json([
                'message' => 'Customer created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
