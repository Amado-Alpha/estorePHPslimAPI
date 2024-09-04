<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;


class CreateUserRequest
{
    private Validator $validator;
  
    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }


    private function setupRules(): void
    {
        $this->validator->rule('required', ['username', 'email', 'password', 'confirm_password']);
        $this->validator->rule('email', 'email');
        
        // Ensure passwords match
        $this->validator->addRule('equals', function ($field, $value, array $params) {
            // Check if confirm_password matches password
            $data = $this->validator->data();
            return isset($data['password']) && $value === $data['password'];
        }, 'Passwords do not match');

        // Apply the equals rule to confirm_password
        $this->validator->rule('equals', 'confirm_password', $this->validator->data()['password'] ?? null);
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
