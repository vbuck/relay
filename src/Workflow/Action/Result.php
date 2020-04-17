<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action;

use Relay\Api\Workflow\Action\ResultInterface;

/**
 * Implementation for {@see ResultInterface}.
 */
class Result implements ResultInterface
{
    /** @var array */
    private $data;

    /** @var bool */
    private $successFlag = false;

    /**
     * @inheritdoc
     */
    public function getData() : array
    {
        return (array) $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getSuccessFlag() : bool
    {
        return (bool) $this->successFlag;
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data = []) : void
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function setSuccessFlag(bool $state) : void
    {
        $this->successFlag = $state;
    }
}
