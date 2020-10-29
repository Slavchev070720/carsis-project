<?php

namespace App\Exception;

class ValidationException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param string $message
     * @param array $errors
     */
    public function __construct(string $message = "", array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
