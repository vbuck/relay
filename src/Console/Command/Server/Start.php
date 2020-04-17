<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Server;

use LowlyPHP\ApplicationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    /** @var ApplicationManager */
    private $app;

    /** @var ServiceLocator */
    private $serviceLocator;

    /**
     * @param ServiceLocator $serviceLocator
     * @param ApplicationManager|null $app
     * @param string|null $name
     */
    public function __construct(
        ServiceLocator $serviceLocator,
        ApplicationManager $app = null,
        ?string $name = null
    ) {
        $this->app = $app ?? ApplicationManager::getInstance();
        $this->serviceLocator = $serviceLocator;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Start the relay web service.');

        $this->addOption(
            'host',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Bind to host',
            '0.0.0.0'
        );

        $this->addOption(
            'port',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Bind to port',
            '8888'
        );

        $this->addOption(
            'root',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Web root path',
            $this->app->getBasePath()
        );

        $this->addOption(
            'router-path',
            't',
            InputOption::VALUE_OPTIONAL,
            'Web root path',
            \rtrim($this->app->getBasePath(), DIRECTORY_SEPARATOR)
                . 'web' . DIRECTORY_SEPARATOR . 'router.php'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = \trim(
            \sprintf(
                '%s start %s%s %s %s',
                $this->serviceLocator->locate(),
                (string) $input->getOption('host'),
                $input->getOption('port') ? ":{$input->getOption('port')}" : '',
                (string) $input->getOption('root'),
                (string) $input->getOption('router-path')
            )
        );

        \trim(\exec($command, $result, $code));
        $output->writeln($result);

        return $code;
    }
}
