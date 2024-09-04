<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;
use App\Repositories\FeatureRepository;

class EditFeatureRequest
{
    private Validator $validator;
    private FeatureRepository $featureRepository;

    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }

    private function setupRules(): void
    {
        $this->validator->rule('required', ['description']);

        // Custom rule to check if feature description exists
    //     $this->validator->addRule('featureExists', function ($field, $value, array $params) {
    //         return !$this->featureRepository->featureExists($value);
    //     }, 'Feature already exists');

    //     $this->validator->rule('featureExists', 'description');
    // 
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
