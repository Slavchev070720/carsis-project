<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Swagger\Annotations as SWG;

/**
 * @Route("/auth")
 */
class ApiAuthController extends AbstractController
{
    const PROJECT_DOMAIN = 'https://carsis-project/';

    /**
     * @Route("/register", name="api_auth_register",  methods={"POST"})
     * @SWG\Post(
     *     summary="Register",
     *     description="Register user and return JWT",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         description="Image must be base64 format!",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             ref="#/definitions/Register"
     *         )
     *      ),
     *     @SWG\Response(
     *         response=307,
     *         description="Redirect to /api/auth/login and return Json Web Token.",
     *         @SWG\Schema(
     *              type="object",
     *               @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="307"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="token"
     *              )
     *          )
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
     * @SWG\Tag(name="Auth")
     *
     * @param UserService $userService
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function register(UserService $userService, Request $request): RedirectResponse
    {
        $response = $userService->registerUser($request);

        return $this->redirectToRoute(
            'api_auth_login',
            $response[UserService::USER_LOGIN_DATA_KEY], Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @Route("/login", name="api_auth_login",  methods={"POST"})
     * @SWG\Post(
     *     summary="Login",
     *     description="Register user and get JWT",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                  type="string",
     *                  property="username"
     *              ),
     *             @SWG\Property(
     *                  type="string",
     *                  property="password"
     *              ),
     *          )
     *      ),
     *     @SWG\Response(
     *         response=200,
     *         description="Return Json Web Token.",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="code",
     *                  example="200"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="token"
     *              )
     *          )
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
     * @SWG\Tag(name="Auth")
     */
    public function login()
    {
        //logic is done in security.yaml
    }
}
