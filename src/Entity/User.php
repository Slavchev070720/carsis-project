<?php

namespace App\Entity;

use App\Controller\ApiAuthController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Model\User as BaseUser;
use JsonSerializable;
use App\Service\UserService;

/**
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already used!"
 *     )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This user is already used!"
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user",indexes={
 *   @ORM\Index(name="username_idx", columns={"username"}),
 * })
 */
class User extends BaseUser implements JsonSerializable
{
    const USERNAME_KEY = 'username';
    const PASSWORD_KEY = 'password';
    const EMAIL_KEY = 'email';
    const SEX_KEY = 'sex';
    const CITY_KEY = 'city';
    const USER_IMAGE_KEY = 'userImage';
    const GROUP_PASSWORD_KEY = 'Password';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Assert\NotBlank(message="Email should not be blank!")
     * @Assert\Email(message="Invalid email!")
     */
    protected $email;

    /**
     * @Assert\NotBlank(message="Username should not be blank!")
     * @Assert\Length(
     *     min = 3,
     *     max = 50,
     *     minMessage = "Username should be atleast {{ limit }} characters long!",
     *     maxMessage = "Username should not be more than {{ limit }} characters long!"
     *     )
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="Password should not be blank!", groups={"Password"})
     * @Assert\Length(
     *     min = 5,
     *     max = 18,
     *     minMessage = "Password should be at least {{ limit }} characters long!",
     *     maxMessage = "Password should not be more than {{ limit }} characters long!"
     *     )
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $jwt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CarAd", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    private $carAds;

    /**
     * @var array
     */
    private $resolvedOptions;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Sex should not be blank!")
     * @Assert\Length(
     *     min = 3,
     *     max = 10,
     *     minMessage = "Sex should be at least {{ limit }} characters long!",
     *     maxMessage = "Sex should not be more than {{ limit }} characters long!"
     *     )
     */
    private $sex;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="City should not be blank!")
     * @Assert\Length(
     *     min = 2,
     *     max = 255,
     *     minMessage = "City should be at least {{ limit }} characters long!",
     *     maxMessage = "City should not be more than {{ limit }} characters long!"
     *     )
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userImage;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct();
        $this->carAds = new ArrayCollection();
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->resolvedOptions = $resolver->resolve($options);
        $this->setUserProperties();
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            self::USERNAME_KEY,
            self::PASSWORD_KEY,
            self::EMAIL_KEY,
            self::SEX_KEY,
            self::CITY_KEY,
            self::USER_IMAGE_KEY
        ]);

        $resolver
            ->setAllowedTypes(self::USERNAME_KEY, 'string')
            ->setAllowedTypes(self::PASSWORD_KEY, 'string')
            ->setAllowedTypes(self::EMAIL_KEY, 'string')
            ->setAllowedTypes(self::SEX_KEY, 'string')
            ->setAllowedTypes(self::CITY_KEY, 'string')
            ->setAllowedTypes(self::USER_IMAGE_KEY, 'string');
    }

    /**
     * Set User properties
     */
    private function setUserProperties(): void
    {
        $this
            ->setUsername($this->resolvedOptions[self::USERNAME_KEY])
            ->setEmail($this->resolvedOptions[self::EMAIL_KEY])
            ->setPlainPassword($this->resolvedOptions[self::PASSWORD_KEY])
            ->setSex($this->resolvedOptions[self::SEX_KEY])
            ->setCity($this->resolvedOptions[self::CITY_KEY])
            ->setUserImage($this->resolvedOptions[self::USER_IMAGE_KEY])
            ->setEnabled(true)
            ->setRoles(['ROLE_USER'])
            ->setSuperAdmin(false);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    /**
     * @param string|null $jwt
     *
     * @return User
     */
    public function setJwt(?string $jwt): self
    {
        $this->jwt = $jwt;

        return $this;
    }

    /**
     * @return Collection|CarAd[]
     */
    public function getCarAds(): Collection
    {
        return $this->carAds;
    }

    /**
     * @param CarAd $carAd
     *
     * @return User
     */
    public function addCarAd(CarAd $carAd): self
    {
        if (!$this->carAds->contains($carAd)) {
            $this->carAds[] = $carAd;
            $carAd->setUser($this);
        }

        return $this;
    }

    /**
     * @param CarAd $carAd
     *
     * @return User
     */
    public function removeCarAd(CarAd $carAd): self
    {
        if ($this->carAds->contains($carAd)) {
            $this->carAds->removeElement($carAd);
            if ($carAd->getUser() === $this) {
                $carAd->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param int $adId
     *
     * @return CarAd
     */
    public function getCarAd(int $adId): CarAd
    {
        $carAdArray = $this->getCarAds()->filter(function (CarAd $carAd) use ($adId) {
            return $carAd->getId() === $adId;
        });
        if (empty($carAdArray->getValues())) {
            throw new UndefinedOptionsException('Car Ad does not exist!');
        }

        return $carAdArray->first();
    }

    /**
     * @param string|null $ids
     *
     * @return string[]
     */
    public function getCarAdsByIDs(?string $ids): array
    {
        if ($ids === null) {
            throw new UndefinedOptionsException ('Invalid get parameter key for delete! Please use get parameter key "id".');
        }
        $checkGetParams = str_replace(',', '', $ids);
        if (!is_numeric($checkGetParams)) {
            throw new UndefinedOptionsException ('Invalid get parameters for delete! Please use integers.');
        }
        $arrayIds = array_flip(explode(',', $ids));
        $carAdArray = $this->getCarAds()->filter(function (CarAd $carAd) use ($arrayIds) {
            return isset($arrayIds[$carAd->getId()]);
        })->getValues();
        if (empty($carAdArray)) {
            throw new UndefinedOptionsException ('Ads with IDs:' . $ids . ' do not exist!');
        }

        return $carAdArray;
    }

    /**
     * @return string|null
     */
    public function getSex(): ?string
    {
        return $this->sex;
    }

    /**
     * @param string $sex
     *
     * @return User
     */
    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserImage(): ?string
    {
        return $this->userImage;
    }

    /**
     * @param string $userImage
     *
     * @return User
     */
    public function setUserImage(string $userImage): self
    {
        $this->userImage = $userImage;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            self::USERNAME_KEY => $this->getUsername(),
            self::EMAIL_KEY => $this->getEmail(),
            self::SEX_KEY => $this->getSex(),
            self::CITY_KEY => $this->getCity(),
            self::USER_IMAGE_KEY => ApiAuthController::PROJECT_DOMAIN . UserService::IMAGE_URI . $this->getUserImage()
        ];
    }
}
