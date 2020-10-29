<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use App\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class Subscriber implements EventSubscriberInterface
{
    const MESSAGE_KEY = 'message';
    const CODE_KEY = 'code';
    const SWAGGER_UI_URI = '/api/doc';

    /**
     * @var array
     */
    private $catchExceptions = [
        'App\Exception\ValidationException' => 0,
        'Symfony\Component\OptionsResolver\Exception\InvalidOptionsException' => 1,
        'Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException' => 2,
        'Symfony\Component\Serializer\Exception\ExceptionInterface' => 3,
        'Symfony\Component\HttpKernel\Exception\BadRequestHttpException' => 4,
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 5,
        'Symfony\Component\OptionsResolver\Exception\MissingOptionsException' => 6,
    ];

    /**
     * @param RequestEvent $requestEvent
     *
     * @throws ValidationException
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();
        if (!$requestEvent->isMasterRequest()
            || $request->getMethod() === Request::METHOD_GET
            || $request->getMethod() === Request::METHOD_DELETE
        ) {
            return;
        }
        if ($request->headers->get('content-type') !== 'application/json') {
            throw new ValidationException('Invalid content-type!Only application/json accepted!');
        }

        if (($request->getMethod() === Request::METHOD_POST || $request->getMethod() === Request::METHOD_POST)
            && !$this->isJsonFormat($request->getContent())) {
            throw new ValidationException('Invalid json format!');
        }

        return;
    }

    /**
     * @param ExceptionEvent $exceptionEvent
     */
    public function onKernelException(ExceptionEvent $exceptionEvent)
    {
        $exception = $exceptionEvent->getException();
        if (isset($this->catchExceptions[get_class($exception)])) {
            $jsonResponse = $this->createJsonResponse(
                Response::HTTP_NOT_ACCEPTABLE,
                [self::MESSAGE_KEY => $this->throwMessageHandler($exception)]
            );

            return $exceptionEvent->setResponse($jsonResponse);
        }
        $jsonResponse = $this->createJsonResponse(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [self::MESSAGE_KEY => "Internal server error!"]
        );

        return $exceptionEvent->setResponse($jsonResponse);
    }

    /**
     * @param ResponseEvent $responseEvent
     *
     * @return Response|void
     */
    public function onKernelResponse(ResponseEvent $responseEvent)
    {
        if ($responseEvent->getRequest()->getRequestUri() === self::SWAGGER_UI_URI
            || $responseEvent->getResponse()->getStatusCode() === Response::HTTP_TEMPORARY_REDIRECT
        ) {
            return;
        }
        $responseContent = json_decode($responseEvent->getResponse()->getContent(), true);
        $newResponseContent = array_merge(
            [self::CODE_KEY => $responseEvent->getResponse()->getStatusCode()],
            $responseContent
        );
        return $responseEvent->getResponse()->setContent(json_encode($newResponseContent));
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private function isJsonFormat(string $string): bool
    {
        if(is_string($string)
            && is_array(json_decode($string, true))
            && (json_last_error() == JSON_ERROR_NONE)
        ){
            return true;
        }

        return false;
    }

    /**
     * @param \Exception $exception
     *
     * @return array|string
     */
    private function throwMessageHandler(\Exception $exception)
    {
        if ($exception instanceof UndefinedOptionsException
            || $exception instanceof InvalidOptionsException
            || $exception instanceof BadRequestHttpException
            || $exception instanceof MissingOptionsException
        ) {
            return str_replace('"', '', $exception->getMessage());
        }

        if ($exception instanceof ValidationException && !empty($exception->getErrors())) {
            return $exception->getErrors();
        }

        if ($exception instanceof NotFoundHttpException) {
            return "Item or Route does not exist!";
        }

        return $exception->getMessage();
    }

    /**
     * @param $code
     * @param $data
     *
     * @return JsonResponse
     */
    private function createJsonResponse(int $code, array $data): JsonResponse
    {
        return new JsonResponse($data, $code);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}