<?php

namespace App\Service;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class AbstractService
{
    /**
     * @var Serializer
     */
    public $serializer;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * @var array
     */
    protected $resolvedOptions;

    /**
     * @param UserManagerInterface $userManager
     * @param OptionsResolver $optionsResolver
     */
    public function __construct(UserManagerInterface $userManager, OptionsResolver $optionsResolver)
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->userManager = $userManager;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * @param array $options
     */
    abstract protected function configureOptions(array $options): void;
}
