<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Digital Certificate Verifier Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aerendir\Component\DigitalCertificateVerifier\Model;

/**
 * Represents a Subject or an Issuer.
 */
final class Identity
{
    /** @var string $commonName Common Name (CN) */
    private string $commonName;

    /** @var string|null $serialNumber Serial Number */
    private ?string $serialNumber = null;

    /** @var string|null Organization Name (O) */
    private ?string $organization = null;

    /** @var array|string|null Organizational Unit Name (OU) This can be an array if the OU is defined multiple times */
    private $organizationalUnit;

    /** @var string|null $businessCategory Business Category (businessCategory) */
    private ?string $businessCategory = null;

    /** @var string|null $streetAddress Street Address */
    private ?string $streetAddress = null;

    /** @var string|null $postalCode Postal Code */
    private ?string $postalCode = null;

    /** @var string|null $locality Locality Name */
    private ?string $locality = null;

    /** @var string|null $stateOrProvince State or Province */
    private ?string $stateOrProvince = null;

    private ?string $country = null;

    /** @var string|null $jurisdictionStateOrProvince Jurisdiction State or Province */
    private ?string $jurisdictionStateOrProvince = null;

    /** @var string|null $jurisdictionCountry Jurisdiction Country */
    private ?string $jurisdictionCountry = null;

    public function __construct(array $identity)
    {
        $this->commonName                  = $identity['commonName'];
        $this->serialNumber                = $identity['serialNumber']                    ?? null;
        $this->organization                = $identity['organizationName']                ?? null;
        $this->organizationalUnit          = $identity['organizationalUnitName']          ?? null;
        $this->businessCategory            = $identity['businessCategory']                ?? null;
        $this->streetAddress               = $identity['streetAddress']                   ?? null;
        $this->postalCode                  = $identity['postalCode']                      ?? null;
        $this->locality                    = $identity['localityName']                    ?? null;
        $this->stateOrProvince             = $identity['stateOrProvinceName']             ?? null;
        $this->country                     = $identity['countryName']                     ?? null;
        $this->jurisdictionStateOrProvince = $identity['jurisdictionStateOrProvinceName'] ?? null;
        $this->jurisdictionCountry         = $identity['jurisdictionCountryName']         ?? null;
    }

    public function getCommonName(): string
    {
        return $this->commonName;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    /**
     * @return array|string|null
     */
    public function getOrganizationalUnit()
    {
        return $this->organizationalUnit;
    }

    public function getBusinessCategory(): ?string
    {
        return $this->businessCategory;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function getStateOrProvince(): ?string
    {
        return $this->stateOrProvince;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getJurisdictionStateOrProvince(): ?string
    {
        return $this->jurisdictionStateOrProvince;
    }

    public function getJurisdictionCountry(): ?string
    {
        return $this->jurisdictionCountry;
    }

    public function toArray(): array
    {
        return [
            'common_name'                         => $this->getCommonName(),
            'serial_number'                       => $this->getSerialNumber(),
            'organization_name'                   => $this->getOrganization(),
            'organizational_unit_name'            => $this->getOrganizationalUnit(),
            'business_category'                   => $this->getBusinessCategory(),
            'street_address'                      => $this->getStreetAddress(),
            'postal_code'                         => $this->getPostalCode(),
            'locality_name'                       => $this->getLocality(),
            'state_or_province_name'              => $this->getStateOrProvince(),
            'country_name'                        => $this->getCountry(),
            'jurisdiction_state_or_province_name' => $this->getJurisdictionStateOrProvince(),
            'jurisdiction_country_name'           => $this->getJurisdictionCountry(),
        ];
    }
}
