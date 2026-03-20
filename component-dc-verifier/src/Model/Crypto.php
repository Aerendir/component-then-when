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
 * Represents the Crypto information returned by the SSL wrapper of `stream_context_create`.
 */
final class Crypto
{
    private string $protocol;
    private string $cipherName;
    private int $cipherBits;
    private string $cipherVersion;

    public function __construct(array $crypto)
    {
        $this->protocol      = $crypto['protocol'];
        $this->cipherName    = $crypto['cipher_name'];
        $this->cipherBits    = $crypto['cipher_bits'];
        $this->cipherVersion = $crypto['cipher_version'];
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function getCipherName(): string
    {
        return $this->cipherName;
    }

    public function getCipherBits(): int
    {
        return $this->cipherBits;
    }

    public function getCipherVersion(): string
    {
        return $this->cipherVersion;
    }

    public function toArray(): array
    {
        return [
            'protocol'       => $this->getProtocol(),
            'cipher_name'    => $this->getCipherName(),
            'cipher_bits'    => $this->getCipherBits(),
            'cipher_version' => $this->getCipherVersion(),
        ];
    }
}
