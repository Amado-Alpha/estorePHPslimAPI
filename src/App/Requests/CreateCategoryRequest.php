<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;
use App\Repositories\CategoryRepository;

class CreateCategoryRequest
{
    private Validator $validator;
    private CategoryRepository $categoryRepository;

    public function __construct(array $data, CategoryRepository $categoryRepository)
    {
        $this->validator = new Validator($data);
        $this->categoryRepository = $categoryRepository;
        $this->setupRules();
    }

    private function setupRules(): void
    {
        $this->validator->rule('required', ['name']);

        // Custom rule to check if category name exists
        $this->validator->addRule('categoryNameExists', function ($field, $value, array $params) {
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
