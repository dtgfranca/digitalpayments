<?php

namespace App\Application;

use App\Domain\Exceptions\DocumentAlreadyExistsException;
use App\Domain\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\UserRepositoryInterface;

class CreateUser
{
    public function __construct(private readonly  UserRepositoryInterface $userRepository){

    }

    public function execute(array $data): void
    {

        $existingUserWithEmail = $this->userRepository->findByEmail($data['email']);
        $existingUserWithCpf = $this->userRepository->findByCpf($data['document']);

        if($existingUserWithEmail) {
            throw new EmailAlreadyExistsException('User already exists');
        }
        if( $existingUserWithCpf) {
            throw new DocumentAlreadyExistsException('Document already exists');
        }
        try{
            $this->userRepository->save($data);
        }catch (\Throwable $e){
            throw new \Exception('Error creating user');
        }

    }
}
