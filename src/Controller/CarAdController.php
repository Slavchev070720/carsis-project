<?php

namespace App\Controller;

use App\Entity\CarAd;
use App\Service\CarAdService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CarAdController extends AbstractController
{
    /**
     * @var CarAdService
     */
    private $carAdService;

    /**
     * @param CarAdService $carAdService
     */
    public function __construct(CarAdService $carAdService)
    {
        $this->carAdService = $carAdService;
    }

    /**
     * @Rest\Route("/carAds", name="api_add_ad", methods={"POST"})
     * @SWG\Post(
     *     summary="Create Car Ad",
     *     description="Create Car Ad",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/CarAd"
     *         )
     *      ),
     *     @SWG\Response(
     *         response=201,
     *         description="Create Car Ad and return image URL",
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/CarAdResponse"
     *         )
     *      ),
     *     @SWG\Response(
     *          response=406,
     *          description="Not accebtable data given.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NotAcceptable"
     *          )
     *      )
     * )
     * @SWG\Tag(name="Car Ads")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws ExceptionInterface
     */
    public function addCarAd(Request $request): JsonResponse
    {
        $response = $this->carAdService->addCarAd($request, $this->getUser());

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Route("/carAds/{id}", name="api_edit_ad", methods={"PUT"})
     * @SWG\Put(
     *     summary="Edit Car Ad",
     *     description="Edit Car Ad",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/CarAd"
     *         )
     *      ),
     *     @SWG\Response(
     *         response=201,
     *         description="Edit Car Ad and return image URL",
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/CarAdResponse"
     *         )
     *      ),
     *     @SWG\Response(
     *          response=406,
     *          description="Not accebtable data given.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NotAcceptable"
     *          )
     *      )
     * )
     * @SWG\Tag(name="Car Ads")
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function editCarAd(Request $request, int $id): JsonResponse
    {

        $response = $this->carAdService->editCarAd($request, $this->getUser(), $id);

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Route("/carAds/records", name="api_delete_ad", methods={"DELETE"})
     * @SWG\Delete(
     *     summary="Delete Car Ads.",
     *     description="Delete one or more Car Ads.",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          in="query",
     *          name="id",
     *          type="string",
     *          description="Delete one or more Car Ads. Multiple ids are separate by coma. Example: id=1,2,3,4",
     *          required=true
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Delete Car Ads and return message with which ads are deleted",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                  type="integer",
     *                  property="code"
     *             ),
     *             @SWG\Property(
     *                  type="string",
     *                  property="message"
     *             )
     *         )
     *      ),
     *     @SWG\Response(
     *          response=406,
     *          description="Not accebtable data given.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NotAcceptable"
     *          )
     *      )
     * )
     * @SWG\Tag(name="Car Ads")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteCarAds(Request $request): JsonResponse
    {
        $response = $this->carAdService->deleteCarAds($request, $this->getUser());

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Route("/carAds/list", name="api_carads_list", methods={"GET"})
     * @SWG\Get(
     *     summary="List Car Ads by different filters",
     *     description="ListCar Ads",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          in="query",
     *          name="brand",
     *          type="string",
     *          description="List Car Ads for a given brand name. Example: 'Audi'"
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="model",
     *          type="string",
     *          description="List Car Ads for a given model name. Example: 'R8'"
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="user",
     *          type="string",
     *          description="List personal Car Ads. Specific value for this query is not required."
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="price",
     *          type="string",
     *          description="List Car Ads by price range. The two prices must be separated by dash '-'. Example: 1000-2000"
     *      ),
     *     @SWG\Parameter(
     *          in="query",
     *          name="search",
     *          type="string",
     *          description="Search by 'Title' which is combination of brandName and modelName. Example: 'Audi R8'",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Return list of all Car Adds for current filters and Pagering Info",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                  type="integer",
     *                  property="code"
     *              ),
     *             @SWG\Property(
     *                  property="pagerInfo",
     *                  ref="#definitions/PagerInfo"
     *              ),
     *             @SWG\Property(
     *              property="carAdList",
     *              type="array",
     *              @SWG\Items(ref="#definitions/CarAdFull")
     *              )
     *         )
     *      ),
     *     @SWG\Response(
     *          response=406,
     *          description="Not accebtable data given.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NotAcceptable"
     *          )
     *      )
     * )
     * @SWG\Tag(name="Car Ads Listing")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getAllCarAdsList(Request $request): JsonResponse
    {
        $response = $this->carAdService->getAllCarAdsList($request, $this->getUser()->getId());

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Route("/carAds/list/{id}", name="api_carad_info", methods={"GET"})
     * @SWG\Get(
     *     summary="Single Car Ad info",
     *     description="Single Car Ad info",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Info of single Car Ad.",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *              type="integer",
     *              property="code"
     *           ),
     *             @SWG\Property(
     *              property="carAdInfo",
     *              ref="#definitions/CarAdFull"
     *           ),
     *         )),
     *     @SWG\Response(
     *          response=406,
     *          description="Not accebtable data given.",
     *          @SWG\Schema(
     *              type="object",
     *              ref="#/definitions/NotAcceptable"
     *          )
     *      )
     * )
     * @SWG\Tag(name="Car Ads Listing")
     * @param CarAd $carAd
     *
     * @return JsonResponse
     */
    public function getCarAd(CarAd $carAd): JsonResponse
    {
        $response = $this->carAdService->getCarAd($carAd);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
