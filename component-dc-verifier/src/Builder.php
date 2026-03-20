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

namespace Aerendir\Component\DigitalCertificateVerifier;

use Aerendir\Component\DigitalCertificateVerifier\Model\DigitalCertificate;

/**
 * Builds a Digital Certificate from a stream or from an exported Digital Certificate to array.
 */
final class Builder
{
    public static function buildFromExport(array $digitalCertificate): DigitalCertificate
    {
        // Rebuilt the chain if present
        $chain = $digitalCertificate['chain'] ?? null;
        if (\is_array($chain)) {
            foreach ($chain as $index => $subDigitalCertificate) {
                $chain[$index] = self::buildFromExport($subDigitalCertificate);
            }
        }

        return new DigitalCertificate(
            $digitalCertificate['raw_certificate'],
            $digitalCertificate['is_ca_trustable'],
            $chain,
            $digitalCertificate['crypto']
        );
    }
}
