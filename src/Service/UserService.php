<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserService extends AbstractService
{
    const USER_LOGIN_DATA_KEY = 'userLoginData';
    const SAVED_IMAGE_ABSOLUTE_PATH = '/var/www/app/public/images/users';
    const IMAGE_URI = 'images/users/';
    const NEW_TOKEN_KEY = 'newToken';

    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var string
     */
    private $uploadUserImageDir;

    /**
     * @param ValidationService $validationService
     * @param UserManagerInterface $userManager
     * @param OptionsResolver $optionsResolver
     * @param ImageService $imageService
     */
    public function __construct(
        ValidationService $validationService,
        UserManagerInterface $userManager,
        OptionsResolver $optionsResolver,
        ImageService $imageService
    ) {
        parent::__construct($userManager, $optionsResolver);
        $this->validationService = $validationService;
        $this->imageService = $imageService;
    }

    /**
     * @param Request $request
     * @return array
     *
     * @throws \Exception
     */
    public function registerUser(Request $request): array
    {
        $data = $this->serializer->decode($request->getContent(), 'json');
        $user = new User($data);
        $this->validationService->validateObject($user, [User::GROUP_PASSWORD_KEY]);
        $imageName = $this->imageService->uploadTempImage($user->getUserImage());
        $user->setUserImage($imageName);
        $this->userManager->updateUser($user);
        $this->imageService->moveImage(self::SAVED_IMAGE_ABSOLUTE_PATH, $imageName);
        $userLoginData = [User::USERNAME_KEY => $user->getUsername(), User::PASSWORD_KEY => $user->getPlainPassword()];

        return [self::USER_LOGIN_DATA_KEY => $userLoginData];
    }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return User
     * @throws ValidationException
     * @throws \Exception
     */
    public function editUser(User $user, Request $request): User
    {
        $this->setResolvedOptions($request->getContent());
        $imageName = $this->imageService->uploadTempImage(
            $this->resolvedOptions[User::USER_IMAGE_KEY],
            $user->getUserImage()
        );
        $this->resolvedOptions[User::USER_IMAGE_KEY] = $imageName;
        $oldImage = $user->getUserImage();
        $this->setUserProperties($user);
        $this->validationService->validateObject($user);
        $this->userManager->updateUser($user);
        if ($imageName !== null) {
            $this->imageService->moveImage(self::SAVED_IMAGE_ABSOLUTE_PATH, $imageName);
            $this->imageService->deleteImage($this->uploadUserImageDir, $oldImage);
        }
        return $user;
    }

    /**
     * @param User $user
     */
    protected function setUserProperties(User $user): void
    {
        $user
            ->setUsername($this->resolvedOptions[User::USERNAME_KEY] ?? $user->getUsername())
            ->setEmail($this->resolvedOptions[User::EMAIL_KEY] ?? $user->getEmail())
            ->setSex($this->resolvedOptions[User::SEX_KEY] ?? $user->getSex())
            ->setCity($this->resolvedOptions[User::CITY_KEY] ?? $user->getCity())
            ->setUserImage($this->resolvedOptions[User::USER_IMAGE_KEY] ?? $user->getUserImage());
    }


    /**
     * @param string $json
     *
     * @throws ValidationException
     */
    protected function setResolvedOptions(string $json): void
    {
        $data = $this->serializer->decode($json, 'json');
        $this->configureOptions($data);
        if (count(array_unique($this->resolvedOptions)) === 1 && end($this->resolvedOptions) === null) {
            throw new ValidationException('Not single valid options for edit is sent! Options: username, password, email, sex, city, userImage.');
        }
    }

    /**
     * @param array $options
     */
    protected function configureOptions(array $options): void
    {
        $this->optionsResolver->setDefaults([
            User::USERNAME_KEY => null,
            User::EMAIL_KEY => null,
            User::SEX_KEY => null,
            User::CITY_KEY => null,
            User::USER_IMAGE_KEY => null,
        ]);

        $this->resolvedOptions = $this->optionsResolver->resolve($options);
    }
}
