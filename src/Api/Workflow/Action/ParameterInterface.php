<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Workflow\Action;

/**
 * Workflow action parameter interface. Contains prepared input for an action to use during execution.
 */
interface ParameterInterface
{
    /**
     * Get the default value.
     *
     * @return string
     */
    public function getDefaultValue() : string;

    /**
     * Get the parameter identifier (code).
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Get the parameter label.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the assigned parameter value.
     *
     * @return string
     */
    public function getValue() : string;

    /**
     * Set the default value.
     *
     * @param string $value
     */
    public function setDefaultValue(string $value) : void;

    /**
     * Set the parameter identifier (code).
     *
     * @param string $id
     */
    public function setId(string $id) : void;

    /**
     * Set the parameter label.
     *
     * @param string $name
     */
    public function setName(string $name) : void;

    /**
     * Set the parameter value.
     *
     * @param string $value
     */
    public function setValue(string $value) : void;
}
