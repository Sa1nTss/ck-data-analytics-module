<?php

namespace App\Entity;

use App\Repository\RolesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $activity = false;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $roles_alt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $auth_list = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $delete = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private string $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRolesAlt(): string
    {
        return $this->roles_alt;
    }

    public function getAuthList(): ?string
    {
        return $this->auth_list;
    }

    public function setRolesAlt($roles_alt): void
    {
        $this->roles_alt = $roles_alt;
    }

    public function setAuthList(?string $auth_list): self
    {
        $this->auth_list = $auth_list;

        return $this;
    }

    public function isActivity(): bool
    {
        return $this->activity;
    }

    public function setActivity(bool $activity): void
    {
        $this->activity = $activity;
    }

    public function getDelete(): bool
    {
        return $this->delete;
    }

    public function setDelete($delete): void
    {
        $this->delete = $delete;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }
}
