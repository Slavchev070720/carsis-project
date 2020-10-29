<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Model;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandService extends AbstractService
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @var ModelRepository
     */
    private $modelRepository;

    /**
     * @param UserManagerInterface $userManager
     * @param OptionsResolver $optionsResolver
     * @param BrandRepository $brandRepository
     * @param ModelRepository $modelRepository
     */
    public function __construct(
        UserManagerInterface $userManager,
        OptionsResolver $optionsResolver,
        BrandRepository $brandRepository,
        ModelRepository $modelRepository
    ) {
        parent::__construct($userManager, $optionsResolver);
        $this->brandRepository = $brandRepository;
        $this->modelRepository = $modelRepository;
    }

    /**
     * @return array
     */
    public function getBrandsList(): array
    {
        return [CarAdService::BRANDS_LIST_KEY => $this->brandRepository->findAll()];
    }

    /**
     * @param Brand $brand
     *
     * @return array
     */
    public function getModelsList(Brand $brand): array
    {
        return [CarAdService::MODELS_LIST_KEY => $brand->getModels()->getValues()];
    }

    /**
     * @param int $brandId
     *
     * @return Brand
     */
    public function getBrand(int $brandId): Brand
    {
        return $this->brandRepository->find($brandId);
    }

    /**
     * @param int $modelId
     *
     * @return Model
     */
    public function getModel(int $modelId): Model
    {
        return $this->modelRepository->find($modelId);
    }

    /**
     * @param array $options
     */
    protected function configureOptions(array $options): void
    {
        // Method not required for this service.
    }
}
