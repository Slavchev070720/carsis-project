<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModelRepository")
 * @ORM\Table(name="model", indexes={
 *     @ORM\Index(name="model_idx", columns={"model_name"}),
 * })
 */
class Model implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modelName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="models")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

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
    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    /**
     * @param string $modelName
     *
     * @return Model
     */
    public function setModelName(string $modelName): self
    {
        $this->modelName = $modelName;

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
     * @return Model
     */
    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

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
            'modelName' => $this->getModelName()
        ];
    }
}
