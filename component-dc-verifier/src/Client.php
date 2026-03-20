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
use Composer\CaBundle\CaBundle;
use SerendipityHQ\Component\ValueObjects\Uri\UriInterface;

use function Safe\readlink;
use function Safe\stream_socket_client;

/**
 * Class Client.
 */
final class Client
{
    /** @var string */
    private const SSL = 'ssl';

    /** @var string */
    private const CRYPTO = 'crypto';

    /**
     * @var array{
     *   ssl: array{
     *     verify_peer: bool,
     *     verify_peer_name: bool,
     *     allow_self_signed: bool,
     *     ciphers: string,
     *     disable_compression: bool,
     *     capture_peer_cert: bool,
     *     capture_peer_cert_chain: bool,
     *     capath?: string,
     *     cafile?: string
     *   }
     * } $contextOptions The options to use to create the stream
     */
    private array $contextOptions;

    /**
     * @var bool|null By default this is true.
     *                If the Client receive an error about the peer verification,
     *                then the digital certificate is issued by an untrusted authority
     *                (not present in the cabundle provided) because the certification
     *                autorithy is non trustworthy or the digital certificate is self
     *                signed.
     */
    private ?bool $isCaTrustable = null;

    public function __construct(?string $caPathOrFile = null)
    {
        $this->contextOptions = [
            self::SSL => [
                'verify_peer'             => true,
                'verify_peer_name'        => true,
                'allow_self_signed'       => false,
                'ciphers'                 => 'HIGH',
                'disable_compression'     => true,
                'capture_peer_cert'       => true,
                'capture_peer_cert_chain' => true,
            ],
        ];

        if (null === $caPathOrFile) {
            $caPathOrFile = CaBundle::getSystemCaRootBundlePath();
        }

        if (\is_dir($caPathOrFile) || (\is_link($caPathOrFile) && \is_dir(readlink($caPathOrFile)))) {
            $this->contextOptions[self::SSL]['capath'] = $caPathOrFile;
        } else {
            // Check the $calist exists
            if (false === \file_exists($caPathOrFile)) {
                throw new \InvalidArgumentException(sprintf('The passed CA list file is missed. Given path is %s', $caPathOrFile));
            }

            $this->contextOptions[self::SSL]['cafile'] = $caPathOrFile;
        }
    }

    /**
     * @return DigitalCertificate|null The DigitalCertificate or null if it is not found
     */
    public function retrieve(UriInterface $domain, bool $allowSelfSigned = false): ?DigitalCertificate
    {
        try {
            $digitalCertificate = $this->retrieveDigitalCertificate($domain, $allowSelfSigned);
        } catch (\ErrorException $errorException) {
            // Catch the exception and verify if it is thrown by a self-signed certificate
            if (null === $this->isCaTrustable) {
                // If the check about self-signed certificates isn't yet done, do it
                $this->checkIfCaIsTrustable($errorException);

                try {
                    // And retry to get again digital certificate without verifying the peer
                    $digitalCertificate = $this->retrieveDigitalCertificate($domain, true);
                } catch (\ErrorException $errorException) {
                    return null;
                }
            } else {
                // Other kind of error occurred: the digital certificate is invalid
                return null;
            }
        }

        return $digitalCertificate;
    }

    /**
     * Checks if the exception thrown is due to an untrusted Certification Authority.
     *
     * The Certification Authorithy is untrastable in two cases:
     *
     * 1. It is not listed in the provided cacert.pem;
     * 2. The digital certificate is self-signed.
     */
    private function checkIfCaIsTrustable(\ErrorException $e): void
    {
        $this->isCaTrustable = false === \strpos($e->getMessage(), 'certificate verify failed');
    }

    private function retrieveDigitalCertificate(UriInterface $domain, bool $allowSelfSigned): DigitalCertificate
    {
        // If self-signed certificates have to be allowed, redefine the context options
        if (true === $allowSelfSigned) {
            $this->contextOptions[self::SSL]['verify_peer']       = false;
            $this->contextOptions[self::SSL]['verify_peer_name']  = false;
            $this->contextOptions[self::SSL]['allow_self_signed'] = true;
        }

        // Set the custom error handler to transform warnings into catchable exceptions
        $this->setCustomErrorHandler();

        try {
            $context = \stream_context_create($this->contextOptions);
            $host    = $domain->getHost();

            if (false === \is_string($host)) {
                throw new \RuntimeException('The host is not present or its value is of the wrong type.');
            }

            $socket = stream_socket_client(
                'ssl://' . $host . ':443',
                $errno,
                $errstr,
                30.0,
                STREAM_CLIENT_CONNECT,
                $context
            );

            $params   = \stream_context_get_params($socket);
            $metaData = \stream_get_meta_data($socket);
        } finally {
            // Restore the system error handler
            $this->restoreNativeErrorHandler();
        }

        if (false === \array_key_exists(self::CRYPTO, $metaData)) {
            $metaData[self::CRYPTO] = null;
        }

        return $this->parseDigitalCertificate($params, $metaData[self::CRYPTO]);
    }

    private function parseDigitalCertificate(array $params, ?array $crypto): DigitalCertificate
    {
        $peerCertificate = $params['options'][self::SSL]['peer_certificate'];
        $parsed          = \openssl_x509_parse($peerCertificate, false);

        if (false === $parsed) {
            throw new \RuntimeException('Impossible to parse the main certificate.');
        }

        $chain = [];

        foreach ($params['options'][self::SSL]['peer_certificate_chain'] as $subCertificate) {
            $parsedSubCertificate = \openssl_x509_parse($subCertificate, false);

            if (false !== $parsedSubCertificate) {
                $chain[] = new DigitalCertificate(
                    $parsedSubCertificate,
                    $this->isCaTrustable ?? true,
                    null,
                    null
                );
            }
        }

        if ([] === $chain) {
            $chain = null;
        }

        return new DigitalCertificate($parsed, $this->isCaTrustable ?? true, $chain, $crypto);
    }

    /**
     * Restores the default error handler.
     */
    private function restoreNativeErrorHandler(): void
    {
        \restore_error_handler();
    }

    /**
     * Stream functions return Warnings instead of Exceptions. So it is impossible to intercept them.
     * This method sets a custom error handler that transforms all Warnings into exception, so they
     * are catchable and can be hndled properly.
     */
    private function setCustomErrorHandler(): void
    {
        \set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if (0 === \error_reporting()) {
                return false;
            }

            throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
        });
    }
}
