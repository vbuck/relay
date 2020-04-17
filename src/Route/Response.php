<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Route;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Service\Resource\SerializerInterface;
use Relay\Api\ResponseInterface;

/**
 * Response implementation for {@see ResponseInterface}.
 */
class Response implements ResponseInterface
{
    /** @var int */
    private $code;

    /** @var string */
    private $message;

    /** @var array */
    private $result;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param int $code
     * @param string $message
     * @param array $result
     * @param SerializerInterface|null $serializer
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function __construct(int $code, string $message, array $result = [], SerializerInterface $serializer = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->result = $result;
        $this->serializer = $serializer
            ?: ApplicationManager::getInstance()->getObject(SerializerInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function getMessage() : string
    {
        return (string) $this->message;
    }

    /**
     * @inheritdoc
     */
    public function getResult() : array
    {
        return (array) $this->result;
    }

    /**
     * @inheritdoc
     */
    public function serialize() : string
    {
        return $this->serializer->serialize(
            [
                'code' => $this->getCode() ?: 200,
                'message' => $this->getMessage(),
                'result' => $this->getResult(),
            ]
        );
    }
}
