<?php

namespace SicrediAPI\Domain;

class Beneficiary {

    const PERSON_KIND_NATURAL = 'person';
    const PERSON_KIND_LEGAL = 'company';

    private $document;
    private $personKind;
    private $name;
    private $address; // Logradouro
    private $city; //
    private $complement; // Complementto
    private $addressNumber; // Numero
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
        string $complement = null,
        string $addressNumber = null,
        string $state = null,
        string $zipCode = null,
        string $phone = null,
        string $email = null)
    {
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

        $this->document = filter_only_numbers($document);
        $this->personKind = $personKind;
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->complement = $complement;
        $this->addressNumber = $addressNumber;
        $this->state = $state;
        $this->zipCode = filter_only_numbers($zipCode);
        $this->phone = filter_only_numbers($phone);
        $this->email = $email;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getPersonKind()
    {
        return $this->personKind;
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

    public function getComplement()
    {
        return $this->complement;
    }

    public function getAddressNumber()
    {
        return $this->addressNumber;
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
