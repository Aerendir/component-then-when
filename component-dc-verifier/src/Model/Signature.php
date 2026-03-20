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
 * Represents the signature.
 */
final class Signature
{
    private int $nid;
    private string $longName;
    private string $shortName;

    public function __construct(int $nid, string $longName, string $shortName)
    {
        $this->nid       = $nid;
        $this->longName  = $longName;
        $this->shortName = $shortName;
    }

    public function getNid(): int
    {
        return $this->nid;
    }

    public function getLongName(): string
    {
        return $this->longName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function toArray(): array
    {
        return [
            'type_NID' => $this->getNid(),
            'type_LN'  => $this->getLongName(),
            'type_SN'  => $this->getShortName(),
        ];
    }
}
