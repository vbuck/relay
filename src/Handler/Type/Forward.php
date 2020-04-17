<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler\Type;

use Relay\Api\Handler\ResultInterface;
use Relay\Api\HandlerInterface;
use Relay\Api\RequestInterface;
use Relay\Handler\ReferenceIdGenerator;
use Relay\Handler\ResultFactory;

/**
 * Forwarding handler. Used to forward a request to an external endpoint.
 */
class Forward implements HandlerInterface
{
    const PARAM_ADDRESS = 'address';
    const PARAM_ALLOW_UNSAFE = 'allow_unsafe';
    const PARAM_HEADERS = 'headers';
    const PARAM_METHOD = 'method';
    const PARAM_PAYLOAD = 'payload';
    const TYPE_CODE = 'forward';

    /** @var ReferenceIdGenerator */
    private $generator;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param ReferenceIdGenerator $generator
     */
    public function __construct(ResultFactory $resultFactory, ReferenceIdGenerator $generator)
    {
        $this->resultFactory = $resultFactory;
        $this->generator = $generator;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function handle(RequestInterface $request) : ResultInterface
    {
        $state = false;
        $method = \strtoupper($request->getParameter(static::PARAM_METHOD) ?: 'GET');
        $address = (string) $request->getParameter(static::PARAM_ADDRESS);
        $payload = (string) $request->getParameter(static::PARAM_PAYLOAD);
        $headers = (string) $request->getParameter(static::PARAM_HEADERS);
        $allowUnsafe = (bool) $request->getParameter(static::PARAM_ALLOW_UNSAFE);

        if ($address) {
            $client = \curl_init($address);
            \curl_setopt($client, CURLOPT_SSL_VERIFYHOST, $allowUnsafe ? 0 : 2);
            \curl_setopt($client, CURLOPT_SSL_VERIFYPEER, (int) !$allowUnsafe);
            \curl_setopt($client, CURLOPT_CUSTOMREQUEST, $method);
            \curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

            if (!empty($payload)) {
                \curl_setopt($client, CURLOPT_POSTFIELDS, $payload);
            }

            if (!empty($headers)) {
                \curl_setopt($client, CURLOPT_HTTPHEADER, (array) @\json_decode($headers));
            }

            $response = (string) \curl_exec($client);
            $state = (int) \curl_getinfo($client, CURLINFO_RESPONSE_CODE) === 200;
        } else {
            $response = 'Failed to provide a forwarding address.';
        }

        return $this->resultFactory->create(self::TYPE_CODE, $response, $state, $this->generator->generate());
    }
}
