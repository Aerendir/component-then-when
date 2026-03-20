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
use Aerendir\Component\DigitalCertificateVerifier\Model\Extensions;
use Aerendir\Component\DigitalCertificateVerifier\Model\Identity;
use Aerendir\Component\DigitalCertificateVerifier\Model\Signature;
use Icecave\Parity\Parity;

/**
 * A rebuilt Digital Certificate with the original one.
 *
 * As the fields of the Digital Certificates may change and be different from one certificate to another,
 * this class helps keep track of such differences and update the library to address them.
 *
 * Internally this class uses Parity.
 */
final class Comparator
{
    /**
     * A comparison always returns an array of differences.
     *
     * If the returned array is empty, then no differences exist.
     */
    public static function compare(DigitalCertificate $originalDc, DigitalCertificate $rebuiltDc): array
    {
        $differences = [];

        if (Parity::isNotEqualTo($originalDc, $rebuiltDc)) {
            if (Parity::isNotEqualTo($originalDc->getRaw(), $rebuiltDc->getRaw())) {
                $differences[] = self::buildDifference('Found differences in "raw"', $originalDc->getRaw(), $rebuiltDc->getRaw());
            }

            if (Parity::isNotEqualTo($originalDc->isCaTrustable(), $rebuiltDc->isCaTrustable())) {
                $differences[] = self::buildDifference('Found differences in the isCaTrustabe.', $originalDc->isCaTrustable(), $rebuiltDc->isCaTrustable());
            }

            if (Parity::isNotEqualTo($originalDc->getHash(), $rebuiltDc->getHash())) {
                $differences[] = self::buildDifference('Found differences in the hash.', $originalDc->getHash(), $rebuiltDc->getHash());
            }

            if (Parity::isNotEqualTo($originalDc->getName(), $rebuiltDc->getName())) {
                $differences[] = self::buildDifference('Found differences in the name.', $originalDc->getName(), $rebuiltDc->getName());
            }

            if (Parity::isNotEqualTo($originalDc->getPurposes(), $rebuiltDc->getPurposes())) {
                $differences[] = self::buildDifference('Found differences in the purposes.', $originalDc->getPurposes(), $rebuiltDc->getPurposes());
            }

            if (Parity::isNotEqualTo($originalDc->getSerialNumber(), $rebuiltDc->getSerialNumber())) {
                $differences[] = self::buildDifference('Found differences in the serial number.', $originalDc->getSerialNumber(), $rebuiltDc->getSerialNumber());
            }

            if (Parity::isNotEqualTo($originalDc->getSignature(), $rebuiltDc->getSignature())) {
                $differences[] = self::buildDifference('Found differences in the serial signature.', $originalDc->getSignature(), $rebuiltDc->getSignature());
            }

            if (Parity::isNotEqualTo($originalDc->getVersion(), $rebuiltDc->getVersion())) {
                $differences[] = self::buildDifference('Found differences in the serial version.', $originalDc->getVersion(), $rebuiltDc->getVersion());
            }

            if (Parity::isNotEqualTo($originalDc->getValidFrom(), $rebuiltDc->getValidFrom())) {
                $differences[] = self::buildDifference('Found differences in the serial valid from.', $originalDc->getValidFrom(), $rebuiltDc->getValidFrom());
            }

            if (Parity::isNotEqualTo($originalDc->getValidTo(), $rebuiltDc->getValidTo())) {
                $differences[] = self::buildDifference('Found differences in the serial valid to.', $originalDc->getValidTo(), $rebuiltDc->getValidTo());
            }

            if (Parity::isNotEqualTo($originalDc->getIssuer(), $rebuiltDc->getIssuer())) {
                $differences[] = self::buildDifference('Found differences in the Issuer.', $originalDc->getIssuer(), $rebuiltDc->getIssuer());
            }

            if (Parity::isNotEqualTo($originalDc->getSubject(), $rebuiltDc->getSubject())) {
                $differences[] = self::buildDifference('Found differences in the Subject.', $originalDc->getSubject(), $rebuiltDc->getSubject());
            }

            if ($originalDc->hasExtensions() && $rebuiltDc->hasExtensions() && Parity::isNotEqualTo($originalDc->getExtensions(), $rebuiltDc->getExtensions())) {
                $differences[] = self::buildDifference('Found differences in the extensions.', $originalDc->getExtensions(), $rebuiltDc->getExtensions());
            }

            if ($originalDc->hasChain() && $rebuiltDc->hasChain() && Parity::isNotEqualTo($originalDc->getChain(), $rebuiltDc->getChain())) {
                $differences[] = self::buildDifference('Found differences in the chain.', $originalDc->getChain(), $rebuiltDc->getChain());
            }
        }

        return $differences;
    }

    public static function renderDifferenceInHtmlWithDump(array $difference): void
    {
        self::renderComparisonHtmlTableWithDump($difference['message'], $difference['original'], $difference['rebuilt']);
    }

    public static function renderComparisonHtmlTableWithDump(string $title, mixed $left, mixed $right): void
    {
        echo '<table width="100%">
            <tr><td colspan="2">' . $title . '</td></tr>'
            . '<tr><td width="50%">';
        \dump($left);
        echo '</td><td width="50%">';
        \dump($right);
        echo '</td></tr></table>';
    }

    private static function buildDifference(string $message, array|bool|string|Signature|int|\DateTimeInterface|Identity|Extensions $original, array|bool|string|Signature|int|\DateTimeInterface|Identity|Extensions $rebuilt): array
    {
        return [
            'message'  => $message,
            'original' => $original,
            'rebuilt'  => $rebuilt,
        ];
    }
}
