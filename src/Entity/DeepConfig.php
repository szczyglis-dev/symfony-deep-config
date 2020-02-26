<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeepConfigRepository")
 */
class DeepConfig
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
    private $param;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }

    public function setParam(string $param): self
    {
        $this->param = $param;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
