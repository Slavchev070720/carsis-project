<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Service\BrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends AbstractController
{
    /**
     * @var BrandService
     */
    private $brandService;

    /**
     * @param BrandService $brandService
     */
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * @Rest\Route("/brands", name="api_brands_list", methods={"GET"})
     * @SWG\Get(
     *     summary="Get brands",
     *     description="Get brands",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="List of car brands.",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="200",
     *              ),
     *              @SWG\Property(
     *                  property="brandList",
     *                  type="array",
     *                  @SWG\Items(ref="#definitions/Brand")
     *              )
     *          )
     *      ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="401",
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="message",
     *                  example="JWT Token not found.",
     *              )
     *          )
     *      )
     * )
     * @SWG\Tag(name="Brands")
     *
     * @return JsonResponse
     */
    public function getBrandsList(): JsonResponse
    {
        $response = $this->brandService->getBrandsList();

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Route("/brands/{id}", name="api_models_list", methods={"GET"})
     * @SWG\Get(
     *     summary="Get models",
     *     description="Get models",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="List of car models for a specific brand.",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="200",
     *              ),
     *              @SWG\Property(
     *                  property="modelList",
     *                  type="array",
     *                  @SWG\Items(ref="#definitions/Model")
     *              )
     *          )
     *      ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized.",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="401"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="message",
     *                  example="JWT Token not found."
     *              )
     *          )
     *      )
     * )
     * @SWG\Tag(name="Models")
     *
     * @param Brand $brand
     *
     * @return JsonResponse
     */
    public function getModelsList(Brand $brand): JsonResponse
    {
        $response = $this->brandService->getModelsList($brand);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
