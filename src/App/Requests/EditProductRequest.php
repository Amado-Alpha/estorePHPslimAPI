<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;

class EditProductRequest
{
    private Validator $validator;

    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }


    private function setupRules(): void
    {
        $this->validator->rule('required', ['name', 'description', 'price', 'category_id', 'image_url']);
        // $this->validator->rule('string', ['name', 'description']);
        $this->validator->rule('numeric', 'price');
        $this->validator->rule('integer', 'category_id');
        $this->validator->rule('url', 'image_url');
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
