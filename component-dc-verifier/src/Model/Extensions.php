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
 * Represents the extensions of the digital certificate.
 */
final class Extensions
{
    private string $authorityKeyIdentifier;
    private string $subjectKeyIdentifier;

    /** @var mixed[]|null */
    private ?array $subjectAltName = null;

    private string $keyUsage;
    private ?string $extendedKeyUsage      = null;
    private ?string $crlDistributionPoints = null;

    /** @var mixed[]|null */
    private ?array $certificatePolicies = null;

    private ?string $authorityInfoAccess = null;
    private string $basicConstraints;
    private ?string $ctPrecertScts = null;

    public function __construct(array $extensions)
    {
        $this->authorityKeyIdentifier = $extensions['authorityKeyIdentifier'];
        $this->subjectKeyIdentifier   = $extensions['subjectKeyIdentifier'];
        $this->subjectAltName         = $extensions['subjectAltName'] ?? null;
        $this->keyUsage               = $extensions['keyUsage'];
        $this->extendedKeyUsage       = $extensions['extendedKeyUsage']      ?? null;
        $this->crlDistributionPoints  = $extensions['crlDistributionPoints'] ?? null;
        $this->certificatePolicies    = $extensions['certificatePolicies']   ?? null;
        $this->authorityInfoAccess    = $extensions['authorityInfoAccess']   ?? null;
        $this->basicConstraints       = $extensions['basicConstraints'];
        $this->ctPrecertScts          = $extensions['ct_precert_scts'] ?? null;
    }

    public function getAuthorityKeyIdentifier(): string
    {
        return $this->authorityKeyIdentifier;
    }

    public function getSubjectKeyIdentifier(): string
    {
        return $this->subjectKeyIdentifier;
    }

    public function getSubjectAltName(): ?array
    {
        return $this->subjectAltName;
    }

    public function getKeyUsage(): string
    {
        return $this->keyUsage;
    }

    public function getExtendedKeyUsage(): ?string
    {
        return $this->extendedKeyUsage;
    }

    public function getCrlDistributionPoints(): ?string
    {
        return $this->crlDistributionPoints;
    }

    public function getCertificatePolicies(): ?array
    {
        return $this->certificatePolicies;
    }

    public function getAuthorityInfoAccess(): ?string
    {
        return $this->authorityInfoAccess;
    }

    public function getBasicConstraints(): string
    {
        return $this->basicConstraints;
    }

    public function getCtPrecertScts(): ?string
    {
        return $this->ctPrecertScts;
    }

    public function toArray(): array
    {
        return [
            'authority_key_identitifier' => $this->getAuthorityKeyIdentifier(),
            'subject_key_identifier'     => $this->getSubjectKeyIdentifier(),
            'subject_alt_name'           => $this->getSubjectAltName(),
            'key_usage'                  => $this->getKeyUsage(),
            'extended_key_usage'         => $this->getExtendedKeyUsage(),
            'crl_distribution_points'    => $this->getCrlDistributionPoints(),
            'certificate_policies'       => $this->getCertificatePolicies(),
            'authority_info_access'      => $this->getAuthorityInfoAccess(),
            'basic_constraints'          => $this->getBasicConstraints(),
            'ct_precert_scts'            => $this->getCtPrecertScts(),
        ];
    }
}
