<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Digital Payments API",
 *     version="1.0.0",
 *     description="API documentation for the Digital Payments system",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 * @OA\Server(
 *     url="/",
 *     description="Local Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
