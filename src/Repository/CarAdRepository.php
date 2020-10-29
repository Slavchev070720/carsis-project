<?php

namespace App\Repository;

use App\Entity\CarAd;
use App\Service\CarAdService;
use App\Service\ValidationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarAd|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarAd|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarAd[]    findAll()
 * @method CarAd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarAdRepository extends ServiceEntityRepository
{
    const CAR_AD_ALIAS = 'ca';
    const BRAND_ALIAS = 'b';
    const MODEL_ALIAS = 'm';
    const USER_ALIAS = 'u';

    private $validationService;

    /**
     * @param ManagerRegistry $registry
     * @param ValidationService $validationService
     */
    public function __construct(ManagerRegistry $registry, ValidationService $validationService)
    {
        parent::__construct($registry, CarAd::class);
        $this->validationService = $validationService;
    }

    /**
     * @param array $whereParams
     *
     * @return Query
     * @throws \Exception
     */
    public function getAllCarAdsQuery(array $whereParams, int $userId): Query
    {
        $query = $this->createQueryBuilder(self::CAR_AD_ALIAS)
            ->addSelect("CONCAT(b.brandName, ' ', m.modelName) AS title")
            ->leftJoin(self::CAR_AD_ALIAS . '.brand', self::BRAND_ALIAS)
            ->leftJoin(self::CAR_AD_ALIAS . '.model', self::MODEL_ALIAS)
            ->leftJoin(self::CAR_AD_ALIAS . '.user', self::USER_ALIAS);

        if (isset($whereParams[CarAdService::GET_KEY_BRAND])) {
            $query->andWhere(self::BRAND_ALIAS . '.brandName= :brand')
                ->setParameter('brand', $whereParams[CarAdService::GET_KEY_BRAND]);
        }
        if (isset($whereParams[CarAdService::GET_KEY_MODEL])) {
            $query->andWhere(self::MODEL_ALIAS . '.modelName= :model')
                ->setParameter('model', $whereParams[CarAdService::GET_KEY_MODEL]);
        }
        if (isset($whereParams[CarAdService::GET_KEY_PRICE_RANGE])) {
            $priceArray = explode('-', $whereParams[CarAdService::GET_KEY_PRICE_RANGE]);
            $query->andWhere(self::CAR_AD_ALIAS . '.price BETWEEN :value1 AND :value2')
                ->setParameter('value1', $priceArray[0] <= $priceArray[1] ? $priceArray[0] : $priceArray[1])
                ->setParameter('value2', $priceArray[1] >= $priceArray[0] ? $priceArray[1] : $priceArray[0]);
        }
        if (isset($whereParams[CarAdService::GET_KEY_USER])) {
            $query->andWhere(self::USER_ALIAS . '.id= :userId')
                ->setParameter('userId', $userId);
        }
        if (isset($whereParams[CarAdService::GET_KEY_SEARCH])) {
            $query->andHaving('title LIKE :title')
                ->setParameter('title', '%' . $whereParams[CarAdService::GET_KEY_SEARCH] . '%');
        }

        return $query->getQuery();
    }
}
