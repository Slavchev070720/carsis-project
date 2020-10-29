<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 * @ORM\Table(name="brand", indexes={
 *     @ORM\Index(name="brand_idx", columns={"brand_name"})
 * })
 */
class Brand implements JsonSerializable
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
    private $brandName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Model", mappedBy="brand", orphanRemoval=true)
     */
    private $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
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
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * @param string $brandName
     *
     * @return Brand
     */
    public function setBrandName(string $brandName): self
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * @return Collection|model[]
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    /**
     * @param model $model
     *
     * @return Brand
     */
    public function addModel(model $model): self
    {
        if (!$this->models->contains($model)) {
            $this->models[] = $model;
            $model->setBrand($this);
        }

        return $this;
    }

    /**
     * @param model $model
     *
     * @return Brand
     */
    public function removeModel(model $model): self
    {
        if ($this->models->contains($model)) {
            $this->models->removeElement($model);
            // set the owning side to null (unless already changed)
            if ($model->getBrand() === $this) {
                $model->setBrand(null);
            }
        }

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
            'brandName' => $this->getBrandName(),
        ];
    }
}
