<?php

declare(strict_types=1);

namespace App\Requests;

use Valitron\Validator;


class EditProjectRequest
{
    private Validator $validator;
  
    public function __construct(array $data)
    {
        $this->validator = new Validator($data);
        $this->setupRules();
    }


    private function setupRules(): void
    {
        $this->validator->rule('required', ['title', 'description']);
        $this->validator->rule('url', 'image_url');

        // Custom rule to check if title and description are strings
        $this->validator->addRule('isString', function ($field, $value) {
            return is_string($value);
        }, 'must be a string');

        $this->validator->rule('isString', ['title', 'description']);

        // Custom rule to check if "features" is an array of positive integer IDs
        if (isset($this->validator->data()["features"])) {
            $this->validator->rule(function($field, $value, $params, $fields) {
                if (!is_array($value)) {
                    return false;
                }
                foreach ($value as $featureId) {
                    if (!is_int($featureId) || $featureId <= 0) {
                        return false;
                    }
                }
                return true;
            }, 'features')->message('Features must be an array of positive integer IDs');
        }

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
