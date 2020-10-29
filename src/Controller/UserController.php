<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Exception\ValidationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/user", name="api_user_info", methods={"GET"})
     * @SWG\Get(
     *     summary="Get user info",
     *     description="Get user info",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="User info.",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *              type="integer",
     *              property="code"
     *           ),
     *             @SWG\Property(
     *              property="userInfo",
     *              ref="#definitions/User"
     *           ),
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
     * @SWG\Tag(name="User")
     *
     * @return JsonResponse
     */
    public function userInfo(): JsonResponse
    {
        return new JsonResponse(['userInfo' => $this->getUser()], Response::HTTP_OK);
    }

    /**
     * @Route("/user", name="api_user_edit", methods={"POST"})
     * @SWG\Post(
     *     summary="Edit user info",
     *     description="Edit user info",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/User"
     *         )
     *      ),
     *     @SWG\Response(
     *         response=201,
     *         description="Return new JWT after successfull profile update.",
     *         @SWG\Schema(
     *              type="object",
     *              ref="#definitions/UserResponse"
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
     * @SWG\Tag(name="User")
     *
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function userEdit(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $user = $this->userService->editUser($this->getUser(), $request);

        return new JsonResponse([UserService::NEW_TOKEN_KEY => $JWTManager->create($user)], Response::HTTP_CREATED);
    }
}