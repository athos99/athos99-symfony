<?php
namespace App\Security;
use Nbgrp\OneloginSamlBundle\Security\User\SamlUserInterface;

class User implements SamlUserInterface
{
// definition saml2 attribut name
    public const USERNAME = 'Login';
    public const ROLES = 'Roles';
    public const FULLNAME = 'FullName';
    public const FIRSTNAME = 'Firstname';
    public const NAME = 'Name';
    public const INITIALS = 'Initials';
    public const EMAIL = 'Email';
    public const OU = 'Ou';
    public const TITLE = 'Title';
    public const PHONE = 'Phone';

    public const AUTHENTIFICATION_SAML = "GINA SAML";
    public const AUTHENTIFICATION_HTTP = "GINA HTTP HEADER";


    /**
     * @var array
     */
    protected array $attributes;
    /**
     * @var string
     */
    protected string $userIdentifier;

    /**
     * @var string[]
     */
    protected array $roles = [];


    /**
     * @var string
     */
    public string $authentificationMethode;

    public function setSamlAttributes(array $attributes) : void
    {
        $this->authentificationMethode=self::AUTHENTIFICATION_SAML;
        $this->attributes = $attributes;
        $this->userIdentifier = mb_strtoupper($attributes[self::USERNAME][0] ?? '');
    }

    public function setHttpAttributes(array $attributes) : void
    {
        $this->authentificationMethode=self::AUTHENTIFICATION_HTTP;
        $this->attributes = $attributes;
        $this->userIdentifier = mb_strtoupper($attributes[self::USERNAME][0] ?? '');
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return string[]
     */
    public function getGinaRoles(): array
    {
        return $this->attributes[self::ROLES] ?? [];
    }


    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->attributes[self::NAME][0] ?? null;
    }


    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->attributes[self::FIRSTNAME][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->attributes[self::FULLNAME][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getInitiales(): ?string
    {
        return $this->attributes[self::INITIALS][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->attributes[self::EMAIL][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getOu(): ?string
    {
        return $this->attributes[self::OU][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->attributes[self::PHONE][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->attributes[self::TITLE][0] ?? null;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getAuthentificationMethode() : string {
        return $this->authentificationMethode;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles) : void
    {
        $this->roles=$roles;
    }

    /**
     * @return void
     */
    public function eraseCredentials() : void
    {
    }
}