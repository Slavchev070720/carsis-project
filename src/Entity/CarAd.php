<?php

namespace App\Entity;

use App\Controller\ApiAuthController;
use App\Service\CarAdService;
use App\Service\UserService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarAdRepository")
 * @ORM\Table(name="car_ad", indexes={
 *     @ORM\Index(name="price_idx", columns={"price"}),
 * })
 */
class CarAd implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\PositiveOrZero(message="Horse power could not be negative number!")
     * @ORM\Column(type="integer")
     */
    private $horsePower;

    /**
     * @Assert\PositiveOrZero(message="Miliage power could not be negative number!")
     * @ORM\Column(type="integer")
     */
    private $miliage;

    /**
     * @Assert\NotBlank(message="Colour could not be empty!")
     * @ORM\Column(type="string", length=255)
     */
    private $colour;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\PositiveOrZero(message="Price power could not be negative number!")
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @Assert\NotBlank(message="Image could not be empty!")
     * @ORM\Column(type="string", length=255)
     */
    private $image;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="carAds")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Model")
     * @ORM\JoinColumn(nullable=false)
     */
    private $model;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return CarAd
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHorsePower(): ?int
    {
        return $this->horsePower;
    }

    /**
     * @param int $horsePower
     *
     * @return CarAd
     */
    public function setHorsePower(int $horsePower): self
    {
        $this->horsePower = $horsePower;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMiliage(): ?int
    {
        return $this->miliage;
    }

    /**
     * @param int $miliage
     *
     * @return CarAd
     */
    public function setMiliage(int $miliage): self
    {
        $this->miliage = $miliage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColour(): ?string
    {
        return $this->colour;
    }

    /**
     * @param string $colour
     *
     * @return CarAd
     */
    public function setColour(string $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return CarAd
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return CarAd
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return CarAd
     */
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return CarAd
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Brand|null
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand|null $brand
     *
     * @return CarAd
     */
    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @param Model|null $model
     *
     * @return CarAd
     */
    public function setModel(?Model $model): self
    {
        $this->model = $model;

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
            'id' => $this->getId(),
            'user' => $this->getUser(),
            'brandName' => $this->getBrand()->getBrandName(),
            'modelName' => $this->getModel()->getModelName(),
            'description' => $this->getDescription(),
            'horsePower' => $this->getHorsePower(),
            'colour' => $this->getColour(),
            'price' => $this->getPrice(),
            'imageUrl' => ApiAuthController::PROJECT_DOMAIN  . UserService::IMAGE_URI . $this->getImage()
        ];
    }
}
