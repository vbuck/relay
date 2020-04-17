<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Factory for {@see ConfirmationQuestion} instances.
 */
class ConfirmationQuestionFactory
{
    /** @var ApplicationManager */
    private $app;

    /**
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Create a new confirmation question object.
     *
     * @param string $question
     * @param bool $default
     * @param string $trueAnswerRegex
     * @return ConfirmationQuestion
     * @throws ConfigException
     */
    public function create(
        string $question,
        bool $default = true,
        string $trueAnswerRegex = '/^y/i'
    ) : ConfirmationQuestion {
        return $this->app->createObject(
            ConfirmationQuestion::class,
            [
                'question' => $question,
                'default' => $default,
                'trueAnswerRegex' => $trueAnswerRegex,
            ]
        );
    }
}
