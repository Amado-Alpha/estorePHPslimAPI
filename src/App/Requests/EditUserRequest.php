<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;


class EditUserRequest
{
    private Validator $validator;
  
    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }


    private function setupRules(): void
    {
        $this->validator->rule('required', ['username', 'email']);
        $this->validator->rule('optional', 'password');
        $this->validator->rule('email', 'email');
    }

    public function validate(): bool
    {
        return $this->validator->validate();
    }


    public function getErrors(): array
    {
        return $this->validator->errors();
    }
}
