<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Handler;

use Relay\Api\HandlerPoolInterface;
use Relay\Route\RequestFactory;
use Relay\Route\ResponseFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    const ARGUMENT_TYPE = 'type';
    const OPTION_HEADERS = 'headers';
    const OPTION_PARAMETERS = 'parameters';
    const OPTION_REQUEST_BODY = 'body';

    /** @var HandlerPoolInterface */
    private $handlerPool;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var ResponseFactory */
    private $responseFactory;

    /**
     * @param RequestFactory $requestFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerPoolInterface $handlerPool
     * @param string|null $name
     */
    public function __construct(
        RequestFactory $requestFactory,
        ResponseFactory $responseFactory,
        HandlerPoolInterface $handlerPool,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->handlerPool = $handlerPool;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Execute a handler directly.');
        $this->addArgument(
            static::ARGUMENT_TYPE,
            InputArgument::REQUIRED,
            'The handler type ID'
        );
        $this->addOption(
            static::OPTION_PARAMETERS,
            'p',
            InputOption::VALUE_OPTIONAL,
            'Parameters as a query string: a=1&b=2'
        );
        $this->addOption(
            static::OPTION_HEADERS,
            'l',
            InputOption::VALUE_OPTIONAL,
            'Request headers as JSON: ["Content-Type: application/json"]'
        );
        $this->addOption(
            static::OPTION_REQUEST_BODY,
            'b',
            InputOption::VALUE_OPTIONAL,
            'Raw request body'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument(static::ARGUMENT_TYPE);
        @\parse_str((string) $input->getOption(static::OPTION_PARAMETERS), $parameters);

        /** @var \Relay\Api\RequestInterface $request */
        $request = $this->requestFactory->create(
            (array) @\json_decode((string) $input->getOption(static::OPTION_HEADERS)),
            (array) $parameters,
            'cli',
            (string) $input->getOption(static::OPTION_REQUEST_BODY)
        );

        /** @var \Relay\Api\HandlerInterface $handler */
        $handler = \current($this->handlerPool->get([$type]));

        if (!$handler) {
            $output->writeln(\sprintf('Handler type "%s" is invalid.', $type));
            return 1;
        }

        /** @var \Relay\Api\Handler\ResultInterface $result */
        $result = $handler->handle($request);
        /** @var \Relay\Api\ResponseInterface $response */
        $response = $this->responseFactory->create(
            $result->isSuccessful() ? 200 : 400,
            $result->isSuccessful() ? 'Handler processed successfully.' : 'Handler execution failed.',
            [
                'type' => $result->getType(),
                'reference_id' => $result->getReferenceId(),
                'timestamp' => $result->getTimestamp(),
                'gateway_response' => $result->getGatewayResponse(),
            ]
        );

        $format = $result->isSuccessful() ? 'info' : 'error';
        $serializedResponse = $response->serialize();

        if (empty($serializedResponse)) {
            $formattedResponse = 'An error occurred during gateway response encoding.';
        } else {
            $formattedResponse = \json_encode(\json_decode($serializedResponse, true), JSON_PRETTY_PRINT);
        }

        $output->writeln(\sprintf("<%s>\n%s</%s>\n", $format, $formattedResponse, $format));

        return !$result->isSuccessful();
    }
}
