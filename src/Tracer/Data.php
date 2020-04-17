<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Tracer\Observer;

use Relay\Api\Tracer\DataInterface;
use SplObserver;

/**
 * Implementation for {@see DataInterface}.
 */
class Data implements DataInterface
{
    /** @var array */
    private $backtrace;

    /** @var object|null */
    private $context;

    /** @var string */
    private $message;

    /** @var \SplObjectStorage */
    private $observers;

    /**
     * @param string $message
     * @param object|null $context
     * @param array $backtrace
     */
    public function __construct(string $message, $context = null, array $backtrace = [])
    {
        $this->observers = new \SplObjectStorage();
        $this->message = $message;
        $this->context = $context;
        $this->backtrace = $backtrace;
    }

    /**
     * {@inheritdoc}
     *
     * Not intended for use, but implementation satisfies interface.
     */
    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     * {@inheritdoc}
     *
     * Not intended for use, but implementation satisfies interface.
     */
    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     * @inheritdoc
     */
    public function getBacktrace() : array
    {
        return (array) $this->backtrace;
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritdoc
     */
    public function getMessage() : string
    {
        return (string) $this->message;
    }

    /**
     * {@inheritdoc}
     *
     * Not intended for use, but implementation satisfies interface.
     */
    public function notify()
    {
        /** @var \SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
