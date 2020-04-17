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
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console application container.
 */
class Application extends ConsoleApplication
{
    const NAME = 'Relay CLI';
    const VERSION = '1.0.0';

    /** @var ApplicationManager */
    private $app;

    /** @var CommandLoader */
    private $commandLoader;

    public function __construct(ApplicationManager $app = null)
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Get the base application path.
     *
     * @return string
     */
    public static function getBasePath() : string
    {
        return \dirname(\dirname(__DIR__));
    }

    /**
     * Run the application.
     *
     * @param  InputInterface|null  $input
     * @param  OutputInterface|null $output
     * @return void
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        \ini_set('display_errors', '1');
        \error_reporting(E_ALL);

        $this->setCommandLoader($this->commandLoader());

        $result = (int) parent::run($input, $output);

        if ($result !== 0) {
            $output->writeln(\sprintf('Execution failed with error code %d.', $result));
        }
    }

    /**
     * Generate the command loader.
     *
     * @return \Symfony\Component\Console\CommandLoader\CommandLoaderInterface
     * @throws \Exception
     */
    protected function commandLoader()
    {
        if (empty($this->commandLoader)) {
            return new CommandLoader();
        }

        return $this->commandLoader;
    }
}
