<?php

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    const ERROR_MESSAGE_KEY = 'error_message';
    const INFO_MESSAGE_KEY = 'message';
    const RESPONSE_CODE_KEY = 'code';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     *
     * @param object $object
     * @param array $validationGroups
     *
     * @throws ValidationException
     */
    public function validateObject(object $object, array $validationGroups = []): void
    {
        $defaultGroup = ['Default'];
        $violations = $this->validator->validate($object,null, array_merge($defaultGroup,$validationGroups));
        if ($violations->count() > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ValidationException('Validator errors!', $messages);
        }
    }

    /**
     * @param string $priceRange
     *
     * @throws \Exception
     */
    public function validatePriceRange(string $priceRange): void
    {
        $helpArray = explode('-', $priceRange);
        if (count($helpArray) !== 2 || !isset($helpArray[0]) || !is_numeric($helpArray[0]) || $helpArray[0] < 0
            || !isset($helpArray[1]) || !is_numeric($helpArray[1]) || $helpArray[1] < 0) {
            throw new ValidationException('Invalid price range!');
        }
    }
}
