<?php

namespace App\Service;

use App\Controller\ApiAuthController;
use App\EventSubscriber\Subscriber;
use App\Exception\ValidationException;
use App\Entity\Brand;
use App\Entity\CarAd;
use App\Entity\Model;
use App\Entity\User;
use App\Repository\BrandRepository;
use App\Repository\CarAdRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CarAdService extends AbstractService
{
    const BRAND_KEY = 'brand';
    const MODEL_KEY = 'model';
    const HORSE_POWER_KEY = 'horsePower';
    const MILIAGE_KEY = 'miliage';
    const COLOUR_KEY = 'colour';
    const DESCRIPTION_KEY = 'description';
    const PRICE_KEY = 'price';

    const IMAGE_KEY = 'image';
    const IMAGE_URL_KEY = 'imageUrl';
    const IMAGE_URI = '/images/carAds';
    const MAX_IMAGE_SIZE = 5;
    const KB_TO_MB = 1048576;

    const ITEMS_PER_PAGE = 20;
    const ITEMS_PER_PAGE_KEY = 'cardsPerPage';

    const GET_KEY_BRAND = 'brand';
    const GET_KEY_MODEL = 'model';
    const GET_KEY_PRICE_RANGE = 'price';
    const GET_KEY_USER = 'user';
    const GET_KEY_SEARCH = 'search';

    const PAGER_INFO_KEY = 'pagerInfo';
    const CAR_ADS_LIST_KEY = 'carAdList';
    const MODELS_LIST_KEY = 'modelsList';
    const BRANDS_LIST_KEY = 'brandsList';
    const CAR_AD_KEY = 'carAdInfo';

    const TOTAL_ADS_KEY = 'totalCarAds';
    const TOTAL_PAGES_KEY = 'totalPages';
    const CURRENT_PAGE_KEY = 'currentPage';

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * @var CarAdRepository
     */
    protected $carAdRepository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var string
     */
    private $uploadAdImageDir;

    /**
     * @param UserManagerInterface $userManager
     * @param ValidationService $validationService
     * @param BrandRepository $brandRepository
     * @param OptionsResolver $optionsResolver
     * @param CarAdRepository $carAdRepository
     * @param PaginatorInterface $paginator
     * @param ImageService $imageService
     */
    public function __construct(
        UserManagerInterface $userManager,
        ValidationService $validationService,
        BrandRepository $brandRepository,
        OptionsResolver $optionsResolver,
        CarAdRepository $carAdRepository,
        PaginatorInterface $paginator,
        ImageService $imageService
    ) {
        parent::__construct($userManager, $optionsResolver);
        $this->validationService = $validationService;
        $this->brandRepository = $brandRepository;
        $this->carAdRepository = $carAdRepository;
        $this->paginator = $paginator;
        $this->imageService = $imageService;
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     * @throws ExceptionInterface
     * @throws ValidationException
     * @throws \Exception
     */
    public function addCarAd(Request $request, User $user): array
    {
        $this->setResolvedOptions($request->getContent());
        $carAd = $this->serializer->denormalize($this->resolvedOptions, CarAd::class);
        $this->flushUserEntity($user, $carAd);
        $this->imageService->moveImage(UserService::SAVED_IMAGE_ABSOLUTE_PATH, $this->resolvedOptions[self::IMAGE_KEY]);

        return [
            Subscriber::MESSAGE_KEY => 'Car Ad registered successfully!',
            self::IMAGE_URL_KEY => $request->getSchemeAndHttpHost() . self::IMAGE_URI . $carAd->getImage()
        ];
    }

    /**
     * @param Request $request
     * @param User $user
     * @param int $adId
     *
     * @return array
     * @throws ValidationException
     * @throws \Exception
     */
    public function editCarAd(Request $request, User $user, int $adId): array
    {
        $carAd = $user->getCarAd($adId);
        $this->setResolvedOptions($request->getContent());
        $oldImage = $carAd->getImage();
        $this->setCarAdProperties($carAd);
        $this->flushUserEntity($user, $carAd);
        if ($this->resolvedOptions[self::IMAGE_KEY] !== null) {
            $this->imageService->moveImage(UserService::SAVED_IMAGE_ABSOLUTE_PATH, $this->resolvedOptions[self::IMAGE_KEY]);
            $this->imageService->deleteImage(UserService::SAVED_IMAGE_ABSOLUTE_PATH, $oldImage);
        }

        return [
            Subscriber::MESSAGE_KEY => 'Car Ad updated successfully!',
            self::IMAGE_URL_KEY => $request->getSchemeAndHttpHost() . self::IMAGE_URI . $carAd->getImage()
        ];
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return array
     */
    public function deleteCarAds(Request $request, User $user): array
    {
        $carAdArray = $user->getCarAdsByIDs($request->get('id'));
        $deletedAdsIds = [];
        $imagesForDelete = [];
        foreach ($carAdArray as $carAd) {
            $deletedAdsIds[] = $carAd->getId();
            $user->removeCarAd($carAd);
            $imagesForDelete[] = $carAd->getImage();
        }
        $deletedAdsIds = implode(',', $deletedAdsIds);
        $this->userManager->updateUser($user);
        foreach ($imagesForDelete as $image) {
            $this->imageService->deleteImage(UserService::SAVED_IMAGE_ABSOLUTE_PATH, $image);
        }

        return [Subscriber::MESSAGE_KEY => 'Car Ad with IDs:' . $deletedAdsIds . ' deleted successfully!'];
    }

    /**
     * @param Request $request
     * @param int $userId
     *
     * @return array
     * @throws \Exception
     */
    public function getAllCarAdsList(Request $request, int $userId): array
    {
        $queryParams = $request->query->all();
        if (isset($queryParams[self::PRICE_KEY])) {
            $this->validationService->validatePriceRange($queryParams[self::PRICE_KEY]);
        }
        $query = $this->carAdRepository->getAllCarAdsQuery($queryParams, $userId);
        $slidingPagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::ITEMS_PER_PAGE,
            ['wrap-queries' => true]
        );
        $pagerInfo = $this->setPagerInfo($slidingPagination);

        return [
            self::PAGER_INFO_KEY => $pagerInfo,
            self::CAR_ADS_LIST_KEY => $this->manageCarAdsDataList($slidingPagination)
        ];
    }

    /**
     * @param CarAd $carAd
     *
     * @return array
     */
    public function getCarAd(CarAd $carAd): array
    {
        return [self::CAR_AD_KEY => $carAd];
    }

    /**
     * @param string $json
     *
     * @throws \Exception
     */
    protected function setResolvedOptions(string $json): void
    {
        $data = $this->serializer->decode($json, 'json');
        $this->configureOptions($data);
        $this->resolvedOptions[self::BRAND_KEY] = $this->getBrand();
        $this->resolvedOptions[self::MODEL_KEY] = $this->getModel($this->resolvedOptions[self::BRAND_KEY]);
        $imageName = $this->imageService->uploadTempImage($this->resolvedOptions[self::IMAGE_KEY]);
        $this->resolvedOptions[self::IMAGE_KEY] = $imageName;
    }

    /**
     * @param User $user
     * @param CarAd $carAd
     *
     * @throws ValidationException
     */
    protected function flushUserEntity(User $user, CarAd $carAd): void
    {
        $this->validationService->validateObject($carAd);
        $user->addCarAd($carAd);
        $this->userManager->updateUser($user);
    }

    /**
     * @param array $options
     */
    protected function configureOptions(array $options): void
    {
        $this->optionsResolver->setDefaults([
            CarAdService::IMAGE_KEY => null,
        ]);

        $this->optionsResolver->setRequired([
            CarAdService::BRAND_KEY,
            CarAdService::MODEL_KEY,
            CarAdService::HORSE_POWER_KEY,
            CarAdService::MILIAGE_KEY,
            CarAdService::COLOUR_KEY,
            CarAdService::DESCRIPTION_KEY,
            CarAdService::PRICE_KEY,
            CarAdService::IMAGE_KEY
        ]);

        $this->optionsResolver
            ->setAllowedTypes(CarAdService::BRAND_KEY, 'integer')
            ->setAllowedTypes(CarAdService::MODEL_KEY, 'integer')
            ->setAllowedTypes(CarAdService::HORSE_POWER_KEY, 'integer')
            ->setAllowedTypes(CarAdService::MILIAGE_KEY, 'integer')
            ->setAllowedTypes(CarAdService::COLOUR_KEY, 'string')
            ->setAllowedTypes(CarAdService::DESCRIPTION_KEY, 'string')
            ->setAllowedTypes(CarAdService::PRICE_KEY, 'integer')
            ->setAllowedTypes(CarAdService::IMAGE_KEY, 'string');

        $this->resolvedOptions = $this->optionsResolver->resolve($options);
    }

    /**
     * @param CarAd $carAd
     */
    private function setCarAdProperties(CarAd $carAd): void
    {
        $carAd
            ->setHorsepower($this->resolvedOptions[self::HORSE_POWER_KEY])
            ->setBrand($this->resolvedOptions[self::BRAND_KEY])
            ->setModel($this->resolvedOptions[self::MODEL_KEY])
            ->setMiliage($this->resolvedOptions[self::MILIAGE_KEY])
            ->setColour($this->resolvedOptions[self::COLOUR_KEY])
            ->setDescription($this->resolvedOptions[self::DESCRIPTION_KEY])
            ->setPrice($this->resolvedOptions[self::PRICE_KEY])
            ->setImage(empty($this->resolvedOptions[self::IMAGE_KEY]) ?
                $carAd->getImage() : $this->resolvedOptions[self::IMAGE_KEY]);
    }

    /**
     * @return Brand
     * @throws ValidationException
     */
    private function getBrand(): Brand
    {
        $brand = $this->brandRepository->find($this->resolvedOptions[self::BRAND_KEY]);

        if ($brand === null) {
            throw new ValidationException('Brand does not exist!');
        }

        return $brand;
    }

    /**
     * @param Brand $brand
     *
     * @return Model
     * @throws ValidationException
     */
    private function getModel(Brand $brand): Model
    {
        $modelId = $this->resolvedOptions[self::MODEL_KEY];

        $models = $brand->getModels()->filter(function (Model $model) use ($modelId) {
            return $model->getId() === $modelId;
        });

        if (empty($models->getValues())) {
            throw new ValidationException('Model does not exist!');
        }

        return $models->first();
    }

    /**
     * @param PaginationInterface $slidingPagination
     *
     * @return array
     */
    private function setPagerInfo(PaginationInterface $slidingPagination): array
    {
        return [
            self::TOTAL_ADS_KEY => $slidingPagination->count(),
            self::ITEMS_PER_PAGE_KEY => $slidingPagination->getItemNumberPerPage(),
            self::TOTAL_PAGES_KEY => ceil($slidingPagination->count() / $slidingPagination->getItemNumberPerPage()),
            self::CURRENT_PAGE_KEY => $slidingPagination->getCurrentPageNumber()
        ];
    }

    /**
     * @param PaginationInterface $slidingPagination
     *
     * @return array
     */
    private function manageCarAdsDataList(PaginationInterface $slidingPagination): array
    {
        $helpArray = [];
        foreach ($slidingPagination->getItems() as $item) {
            $helpArray[] = reset($item);
        }

        return $helpArray;
    }
}
