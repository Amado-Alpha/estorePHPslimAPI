<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;
use App\Repositories\CategoryRepository;


class CreateProductRequest
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
        $this->validator->rule('required', ['name', 'description', 'price', 'category_id', 'image_url']);
        // $this->validator->rule('required', ['name', 'description']);
        $this->validator->rule('numeric', 'price');
        $this->validator->rule('integer', 'category_id');
        $this->validator->rule('url', 'image_url');

       
        // Custom rule to check if category ID exists
        $this->validator->addRule('categoryIdExists', function ($field, $value) {
            return $this->categoryRepository->categoryIdExists((int)$value);
        }, 'The category ID does not exist.');

        $this->validator->rule('categoryIdExists', 'category_id');
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
