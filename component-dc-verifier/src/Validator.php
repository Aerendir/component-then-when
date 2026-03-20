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
use SerendipityHQ\Component\ValueObjects\Uri\UriInterface;

use function Safe\preg_match;

/**
 * Helps to validate a digital certificate.
 */
final class Validator
{
    public static function isActive(DigitalCertificate $digitalCertificate): bool
    {
        return $digitalCertificate->getValidFrom() < self::getNow();
    }

    /**
     * Returns false if the connection isn't secure.
     */
    public static function isSecureConnection(DigitalCertificate $digitalCertificate, UriInterface $domain): bool
    {
        if (self::isMismatched($digitalCertificate, $domain)) {
            return false;
        }

        if (false === $digitalCertificate->isCaTrustable()) {
            return false;
        }

        if (self::isExpired($digitalCertificate)) {
            return false;
        }

        if (false === self::isActive($digitalCertificate)) {
            return false;
        }

        return ! self::usesUnsecureAlgorithms($digitalCertificate);
    }

    public static function isExpired(DigitalCertificate $digitalCertificate): bool
    {
        return $digitalCertificate->getValidTo() < self::getNow();
    }

    public static function isMismatched(DigitalCertificate $digitalCertificate, UriInterface $domain): bool
    {
        $host = $domain->getHost();
        if (null === $host) {
            throw new \LogicException("The given Domain doesn't have an host: impossible to verify if the digital certificate is mismatched.");
        }

        $simpleComparison = static fn (string $name): bool => $name === $host;

        $wildcardComparison = static function (string $name) use ($host): bool {
            $regEx          = self::glob2regex($name);
            $matches        = preg_match($regEx, $host);

            // Returns true if at least 1 match is found
            return 0 < $matches;
        };

        // Build the names to match against
        $names = [];
        if ($digitalCertificate->hasExtensions()) {
            $names = $digitalCertificate->getExtensions()->getSubjectAltName() ?? [];
        }
        $names[] = $digitalCertificate->getSubject()->getCommonName();

        // Check the common name and each alt name
        /** @var string $name */
        foreach ($names as $name) {
            // Remove "DNS:" prefix from SAN
            $cleanName = \str_replace('DNS:', '', $name);

            // If we need to use the glob pattern or not
            $result = false !== \strpos($cleanName, '*')
                ? $wildcardComparison($cleanName)
                : $simpleComparison($cleanName);

            // If the host is found in the name
            if (true === $result) {
                // Directly return false (the certificate matches the domain)
                return false;
            }
        }

        return true;
    }

    /**
     * The RSA-SHA1 algorithm is deprecated.
     * See this for more info:
     * https://community.qualys.com/blogs/securitylabs/2014/09/09/sha1-deprecation-what-you-need-to-know.
     *
     * Google is shutting down it after 2016 and during 2016 it will alert Chrome's users if the site they
     * are visiting uses it in its certificate chain.
     * So we'll do the same: we'll alert users that the site they are visiting uses SHA1 algorithm.
     *
     * So it is appropriate to show
     *
     * Security calculation is based on the document published by SSLLabs:
     * https://www.ssllabs.com/downloads/SSL_Server_Rating_Guide.pdf
     */
    public static function isSha1Used(DigitalCertificate $digitalCertificate): bool
    {
        if ('RSA-SHA1' === $digitalCertificate->getSignature()->getShortName()) {
            return true;
        }

        // If this is an intermediate certificate it hasn't a chain
        if ($digitalCertificate->hasChain()) {
            foreach ($digitalCertificate->getChain() as $intermediateCertificate) {
                if ('RSA-SHA1' === $intermediateCertificate->getSignature()->getShortName()) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function isTimely(DigitalCertificate $digitalCertificate): bool
    {
        return false === self::isExpired($digitalCertificate) && self::isActive($digitalCertificate);
    }

    /**
     * For the moment this method only checks SHA1.
     *
     * More algorithms will be added as needed.
     */
    public static function usesUnsecureAlgorithms(DigitalCertificate $digitalCertificate): bool
    {
        return self::isSha1Used($digitalCertificate);
    }

    private static function getNow(): \DateTime
    {
        $timezone  = new \DateTimeZone('UTC');

        return new \DateTime('now', $timezone);
    }

    private static function glob2regex(string $globPatt): string
    {
        return '/^' . \preg_replace_callback('#.#u', static function (array $m): string {
            switch ($m[0]) {
                case '*': return '[^.]+';
                case '?': return '.';
            }

            return \preg_quote($m[0], '/');
        }, $globPatt) . '$/AsS';
    }
}
