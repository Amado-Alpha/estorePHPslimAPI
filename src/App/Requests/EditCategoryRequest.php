<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;
use App\Repositories\CategoryRepository;

class EditCategoryRequest
{
    private Validator $validator;

    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }


    private function setupRules(): void
    {
        $this->validator->rule('required', ['name']);
        
        // Custom rule to check if category name exists
        $this->validator->addRule('categoryNameExists', function ($field, $value, array $params, Validator $v) {
            // Use the repository to check if the category name exists in the database
            return !$this->categoryRepository->categoryNameExists($value);
        }, 'The category name already exists');

        $this->validator->rule('categoryNameExists', 'name');
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
