<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler\Type\GitHub;

/**
 * GitHub web hook authorization utility.
 */
class Authorization
{
    /** @var string[] */
    private $supportedAlgorithms;

    public function __construct()
    {
        $this->supportedAlgorithms = \hash_hmac_algos();
    }

    /**
     * Authorize a GitHub web hook by its signature and payload.
     *
     * @see https://developer.github.com/webhooks/#delivery-headers
     * @param string $token The token used to sign the request.
     * @param string|null $signature The signature provided by X-Hub-Signature.
     * @param string $input The raw request payload.
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function authorize(string $token = '', string $signature = null, string $input = '') : bool
    {
        $signature = \explode('=', $signature ?: '=');

        if (\count($signature) !== 2) {
            throw new \InvalidArgumentException('GitHub signature format is invalid.');
        }

        $algorithm = $signature[0];
        $requestToken = $signature[1];

        $this->verifyAlgorithm($algorithm);

        return empty($token) || $requestToken === \hash_hmac($algorithm, $input, $token);
    }

    /**
     * Check the given algorithm for support.
     *
     * @param string $algorithm
     * @return void
     * @throws \InvalidArgumentException
     */
    private function verifyAlgorithm(string $algorithm = '') : void
    {
        if (!\in_array($algorithm, $this->supportedAlgorithms)) {
            throw new \InvalidArgumentException(
                \sprintf('"%s" is not a supported algorithm.', $algorithm)
            );
        }
    }
}
