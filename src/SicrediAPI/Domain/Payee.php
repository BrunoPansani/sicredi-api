<?php

namespace SicrediAPI\Domain;

class Payee
{
    public const PERSON_KIND_NATURAL = 'person';
    public const PERSON_KIND_LEGAL = 'company';

    private $personKind;
    private $document;
    private $name;
    private $address;
    private $city;
    private $state;
    private $zipCode;
    private $phone;
    private $email;

    public function __construct(
        string $name,
        string $document,
        string $personKind,
        string $address = null,
        string $city = null,
        string $state = null,
        string $zipCode = null,
        string $phone = null,
        string $email = null
    ) {
        // Person Kind must be one of the constants
        if (!in_array($personKind, [self::PERSON_KIND_NATURAL, self::PERSON_KIND_LEGAL])) {
            throw new \InvalidArgumentException("Person Kind must be one of 'person' or 'company'");
        }

        // UF must be one of BRASIL's states
        if (!empty($state) && !valid_brazilian_state($state)) {
            throw new \InvalidArgumentException("UF must be the abbreviation of a Brazilian state");
        }

        // E-mail must be valid
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail must be valid");
        }

        $this->personKind = $personKind;
        $this->document = filter_only_numbers($document);
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zipCode = filter_only_numbers($zipCode);
        $this->phone = filter_only_numbers($phone);
        $this->email = $email;
    }

    public function getPersonKind()
    {
        return $this->personKind;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
