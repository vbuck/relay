<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Profile;

use Relay\Data\Profile;
use Relay\Data\ProfileRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    const ARGUMENT_NAME = 'name';
    const ARGUMENT_DESCRIPTION = 'description';
    const ARGUMENT_STATUS = 'status';
    const ARGUMENT_TYPE_ID = 'type-id';
    const ARGUMENT_WORKFLOW_ID = 'workflow-id';

    /** @var ProfileRepository */
    private $profileRepository;

    /**
     * @param ProfileRepository $profileRepository
     * @param string|null $name
     */
    public function __construct(ProfileRepository $profileRepository, ?string $name = null)
    {
        parent::__construct($name);

        $this->profileRepository = $profileRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Create a new relay profile.');
        $this->addArgument(
            static::ARGUMENT_NAME,
            InputArgument::REQUIRED,
            'Profile name'
        );
        $this->addArgument(
            static::ARGUMENT_STATUS,
            InputArgument::REQUIRED,
            \sprintf('Profile status: %s, %s', Profile::STATUS_ENABLED, Profile::STATUS_DISABLED)
        );
        $this->addArgument(
            static::ARGUMENT_TYPE_ID,
            InputArgument::REQUIRED,
            'Profile handler type code'
        );
        $this->addArgument(
            static::ARGUMENT_WORKFLOW_ID,
            InputArgument::REQUIRED,
            'Associated workflow ID'
        );
        $this->addArgument(
            static::ARGUMENT_DESCRIPTION,
            InputArgument::OPTIONAL,
            'Profile description'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Profile $profile */
        $profile = $this->profileRepository->create(
            [
                Profile::NAME => $input->getArgument(static::ARGUMENT_NAME),
                Profile::DESCRIPTION => $input->getArgument(static::ARGUMENT_DESCRIPTION),
                Profile::STATUS => $input->getArgument(static::ARGUMENT_STATUS),
                Profile::TYPE_ID => $input->getArgument(static::ARGUMENT_TYPE_ID),
                Profile::WORKFLOW_ID => $input->getArgument(static::ARGUMENT_WORKFLOW_ID),
            ]
        );

        if ($profile->getEntityId() > 0) {
            $output->writeln(\sprintf('Created profile #%d.', $profile->getEntityId()));
            return 0;
        }

        $output->writeln('Failed to create profile');
        return 1;
    }
}
