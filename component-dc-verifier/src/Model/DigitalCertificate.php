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

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class DigitalCertificate.
 *
 * This class repsents a Digital Certificate.
 * The specifications of a Digital Certificate are described by RFC3280 (see Section 4).
 *
 * The is* methods are based on the evaluations made by SSLLabs that checks some parameters
 * of the SSL configuration to calculate a score.
 * Details about their use-of-proceeds can be found here:
 * https://www.ssllabs.com/downloads/SSL_Server_Rating_Guide.pdf
 */
final class DigitalCertificate
{
    /** @var array The array containing the fileds as returned by openssl_x509_parse() */
    private array $rawCertificate;

    /** @var Crypto|null The information about the cypher. This can be null when creating an intermediate DigitalCertificate. */
    private ?Crypto $crypto = null;

    /** @var DigitalCertificate[]|null */
    private ?array $chain = null;

    private ?Extensions $extensions = null;
    private string $hash;
    private Identity $issuer;
    private string $name;
    private array $purposes;
    private string $serialNumber;
    private Signature $signature;
    private Identity $subject;
    private \DateTimeInterface $validFrom;
    private \DateTimeInterface $validTo;
    private int $version;
    private bool $isCaTrustable = false;

    /**
     * @param DigitalCertificate[]|null $chain
     */
    public function __construct(array $rawCertificate, bool $isCaTrustable, ?array $chain = null, ?array $crypto = null)
    {
        $this->rawCertificate = $rawCertificate;
        $this->crypto         = null !== $crypto ? new Crypto($crypto) : null;
        $this->isCaTrustable  = $isCaTrustable;
        $this->chain          = $chain;
        $this->extensions     = $this->normalizeExtensions($this->rawCertificate['extensions'] ?? null);
        $this->hash           = $this->rawCertificate['hash'];
        $this->issuer         = new Identity($this->rawCertificate['issuer']);
        $this->name           = $this->rawCertificate['name'];
        $this->purposes       = $this->rawCertificate['purposes'];
        $this->serialNumber   = $this->rawCertificate['serialNumber'];
        $this->signature      = new Signature($this->rawCertificate['signatureTypeNID'], $this->rawCertificate['signatureTypeLN'], $this->rawCertificate['signatureTypeSN']);
        $this->subject        = new Identity($this->rawCertificate['subject']);
        $this->version        = $this->rawCertificate['version'];

        // See this for details and helps: http://stackoverflow.com/a/34069419/1399706
        $this->validFrom = new \DateTime('@' . $this->rawCertificate['validFrom_time_t']);
        $this->validTo   = new \DateTime('@' . $this->rawCertificate['validTo_time_t']);
    }

    /**
     * Verify if the certification authority is trustable.
     *
     * If false it means the certification authority isn't listed in the Mozilla's cacert.pem
     * and digital certificate is signed by an untrastable CA or is self-signed.
     *
     * Activation criteria:
     * - extensions.authorityKeyIdentifier is not set (this MAY be possible if is self-signed,
     *   as described in Section 4.2.1.1 of RFC 3280)
     *
     * @return bool True if is self-signed, false instead
     */
    public function isCaTrustable(): bool
    {
        return $this->isCaTrustable;
    }

    /**
     * @return DigitalCertificate[]
     */
    public function getChain(): array
    {
        if (null === $this->chain) {
            throw new \LogicException('This Digital Certificate has no chain. Please, use DigitalCertificate::hasChain() before calling this method.');
        }

        return $this->chain;
    }

    public function hasChain(): bool
    {
        return null !== $this->chain;
    }

    public function getCrypto(): Crypto
    {
        if ( ! $this->crypto instanceof Crypto) {
            throw new \LogicException('This Digital Certificate has no crypto. Please, use DigitalCertificate::hasCrypto() before calling this method.');
        }

        return $this->crypto;
    }

    public function hasCrypto(): bool
    {
        return null !== $this->crypto;
    }

    public function getExtensions(): Extensions
    {
        if ( ! $this->extensions instanceof Extensions) {
            throw new \LogicException('This Digital Certificate has no extensions. Please, use DigitalCertificate::hasExtensions() before calling this method.');
        }

        return $this->extensions;
    }

    public function hasExtensions(): bool
    {
        return null !== $this->extensions;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getIssuer(): Identity
    {
        return $this->issuer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPurposes(): array
    {
        return $this->purposes;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    public function getSignature(): Signature
    {
        return $this->signature;
    }

    public function getSubject(): Identity
    {
        return $this->subject;
    }

    public function getValidFrom(): \DateTimeInterface
    {
        return $this->validFrom;
    }

    public function getValidTo(): \DateTimeInterface
    {
        return $this->validTo;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getRaw(): array
    {
        return $this->rawCertificate;
    }

    public function toArray(): array
    {
        $digitalCertificate = [
            'raw_certificate' => $this->getRaw(),
            'is_ca_trustable' => $this->isCaTrustable(),
            'hash'            => $this->getHash(),
            'issuer'          => $this->getIssuer()->toArray(),
            'name'            => $this->getName(),
            'purposes'        => $this->getPurposes(),
            'serial_number'   => $this->getSerialNumber(),
            'signature'       => $this->getSignature()->toArray(),
            'subject'         => $this->getSubject()->toArray(),
            'version'         => $this->getVersion(),
            'valid_from'      => $this->getValidFrom()->getTimestamp(),
            'valid_to'        => $this->getValidTo()->getTimestamp(),
        ];

        // Process Crypto
        $crypto = null;
        if ($this->hasCrypto()) {
            $crypto = $this->getCrypto()->toArray();
        }

        $digitalCertificate['crypto'] = $crypto;

        // Process chains
        if ($this->hasChain()) {
            $chain = [];
            foreach ($this->getChain() as $subDigitalCertificate) {
                $chain[] = $subDigitalCertificate->toArray();
            }

            $digitalCertificate['chain'] = $chain;
        }

        // Process Extensions
        $extensions = null;
        if ($this->hasExtensions()) {
            $extensions = $this->getExtensions()->toArray();
        }

        $digitalCertificate['extensions'] = $extensions;

        return $digitalCertificate;
    }

    private function normalizeExtensions(?array $extensions): ?Extensions
    {
        if (null === $extensions || [] === $extensions) {
            return null;
        }

        // Use PropertyAccess to avoid multiple checks on existence of keys
        $propertyAccess = PropertyAccess::createPropertyAccessor();

        // Normalize SubjectAlternativeName RFC3280, Section 4.2.1.7
        /** @var string|null $subjectAlternativeNames */
        $subjectAlternativeNames = $propertyAccess->getValue($extensions, '[subjectAltName]');
        if (\is_string($subjectAlternativeNames)) {
            // 1) Remove "DNS:" part
            $subjectAlternativeNames = \str_replace('DNS:', '', $subjectAlternativeNames);

            // 2) Split into an array
            $subjectAlternativeNames = \explode(', ', $subjectAlternativeNames);

            // 3) Add again to the $extensions array
            $propertyAccess->setValue($extensions, '[subjectAltName]', $subjectAlternativeNames);
        }

        // Normalize certificatePolicies RFC3280, Section 4.2.1.5
        /** @var string|null $certificatePolicies */
        $certificatePolicies = $propertyAccess->getValue($extensions, '[certificatePolicies]');
        if (\is_string($certificatePolicies)) {
            // 1) Remove "\n"
            $certificatePolicies = \str_replace("\n", '', $certificatePolicies);

            // 2) Split policies
            $certificatePolicies = \explode('Policy: ', $certificatePolicies);

            // 3) Remove the first empty element
            \array_shift($certificatePolicies);

            // 3) Add again to the $extensions array
            $propertyAccess->setValue($extensions, '[certificatePolicies]', $certificatePolicies);
        }

        return new Extensions((array) $extensions);
    }
}
