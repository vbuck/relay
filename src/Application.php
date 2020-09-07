<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Service\ApplicationInterface;
use Relay\Api\RequestInterface;
use Relay\Api\RouteInterface;
use Relay\Route\ContextFactory;
use Relay\Route\RequestFactory;
use Relay\Route\ResponseFactory;
use Relay\Service\WebApiException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Web API based application container. Creates a service for the storefront.
 */
class Application implements ApplicationInterface
{
    const AREA = 'webapi';

    /** @var ApplicationManager */
    private $appManager;

    /** @var ContextFactory */
    private $contextFactory;

    /** @var bool */
    private $debugMode;

    /** @var array */
    private $headers;

    /** @var RequestInterface */
    private $request;

    /** @var string */
    private $requestPath;

    /** @var \Relay\Api\ResponseInterface */
    private $response;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var ResponseFactory */
    private $responseFactory;

    /**
     * {@inheritdoc}
     */
    public function run(ApplicationManager $appManager = null) : void
    {
        \ob_start();

        $this->appManager = $appManager ?? ApplicationManager::getInstance();
        $this->setMode();

        $this->contextFactory = $this->appManager->getObject(ContextFactory::class);
        $this->responseFactory = $this->appManager->getObject(ResponseFactory::class);
        $this->requestFactory = $this->appManager->getObject(RequestFactory::class);

        $this->initHeaders();
        $this->initRequest();

        try {
            $this->preflight();
            $this->route();
        } catch (WebApiException $error) {
            $message = $error->getMessage();

            if ($this->debugMode && $error->getPrevious()) {
                $message .= \sprintf(
                    '; wraps %s:\n%s\n%s',
                    get_class($error->getPrevious()),
                    $error->getPrevious()->getMessage(),
                    $error->getPrevious()->getTraceAsString()
                );
            }

            $this->response = $this->responseFactory->create($error->getCode(), $message);
        } catch (\Exception $error) {
            if ($this->debugMode) {
                throw $error;
            } else {
                $this->response = $this->responseFactory->create(
                    500,
                    'Internal service error.'
                );
            }
        }

        \ob_end_clean();
        $this->send();
    }

    private function setMode()
    {
        $this->debugMode = $this->appManager->config('dev.debug_mode');

        if ($this->debugMode) {
            if (\class_exists(\Whoops\Run::class)) {
                $whoops = new \Whoops\Run();
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
                $whoops->register();
            }
        }
    }

    /**
     * Get the configured base web application URL.
     *
     * @return string
     */
    private function getBaseUrl() : string
    {
        try {
            $url = $this->appManager->config('web.url');
        } catch (\Exception $error) {
            $url = null;
        }

        if (empty($url)) {
            $url = $this->getRequestedBaseUrl();
        }

        return $url;
    }

    /**
     * Convert the given path to a class name.
     *
     * Works by converting the request path to an internal route handler class name.
     *
     * Example: /api/status.json -> \Relay\Api\Status::execute
     * Example: /api/some-name.json -> \Relay\Api\SomeName::execute
     * Example: /api/other/endpoint.json -> \Relay\Api\Other\Endpoint::execute
     *
     * @param string $route
     * @return string
     */
    private function getClassByRoute(string $route) : string
    {
        $class = __NAMESPACE__;
        $path = \preg_replace('/\.[aA-zZ0-9]+$/', '', $route);
        $components = \explode('/', $path);

        \array_walk(
            $components,
            function (&$component) {
                $component = \str_replace(
                    ' ',
                    '',
                    \ucwords(str_replace('-', ' ', $component))
                );
            }
        );

        $class .= \implode('\\', $components);

        return $class;
    }

    /**
     * Get the requested base URL.
     *
     * @return string
     */
    private function getRequestedBaseUrl() : string
    {
        return ($this->isSecure() ? 'https' : 'http')
            . '://'
            . \rtrim($_SERVER['HTTP_HOST'], '/')
            . '/';
    }

    /**
     * Set the base headers for each request.
     */
    private function initHeaders() : void
    {
        $this->setHeader('Access-Control-Allow-Origin', $this->getBaseUrl());
        // @todo future support of content-type by mapping extension in request path
        $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * Prepare the request for routing.
     * @throws \LowlyPHP\Exception\ConfigException
     */
    private function initRequest() : void
    {
        $this->requestPath = \rtrim($_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '/'), '/');
        $httpFoundation = Request::createFromGlobals();
        $this->request = $this->requestFactory->create(
            \array_map(
                function ($value) {
                    return \current((array) $value);
                },
                (array) $httpFoundation->headers->all()
            ),
            (array) $httpFoundation->request->all() + (array) $httpFoundation->query->all(),
            (string) $httpFoundation->server->get('HTTP_RERFERER'),
            (string) $httpFoundation->getContent()
        );
    }

    /**
     * Determine whether the request is secure.
     *
     * @return bool
     */
    private function isSecure() : bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    /**
     * Perform application runtime pre-flight checks.
     *
     * @throws WebApiException
     */
    private function preflight() : void
    {
        $components = ['host' => ''];
        $urls = [
            \array_merge($components, \array_filter(\parse_url($this->getBaseUrl()))),
            \array_merge($components, \array_filter(\parse_url($_SERVER['HTTP_REFERER'] ?? ''))),
        ];

        if (!empty($urls[1]['host']) && \strcasecmp($urls[0]['host'], $urls[1]['host']) !== 0) {
            throw new WebApiException(\sprintf('Unauthorized request origin "%s"', $urls[1]['host']), 401);
        }
    }

    /**
     * Route the request.
     *
     * @throws WebApiException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    private function route() : void
    {
        $class = $this->getClassByRoute($this->requestPath);
        $route = null;

        try {
            /** @var RouteInterface $route */
            if (\class_exists($class)) {
                $route = $this->appManager->getObject($class);
            }
        } catch (\Exception $error) {
            $route = null;
        }

        if (!($route instanceof RouteInterface)) {
            throw new WebApiException(
                \sprintf('Requested endpoint "%s" does not exist', $this->requestPath),
                404
            );
        }

        $urlComponents = \parse_url($this->getRequestedBaseUrl());
        $context = $this->contextFactory->create(
            $urlComponents['host'],
            static::AREA,
            $this->isSecure()
        );

        $this->response = $route->execute($this->request, $context);
    }

    /**
     * Send the response to the client.
     */
    private function send() : void
    {
        foreach ($this->headers as $name => $value) {
            \header(\addslashes("{$name}: {$value}"));
        }

        echo $this->response->serialize();

        exit(0);
    }

    /**
     * Set a custom header.
     *
     * @param string $name
     * @param string $value
     */
    private function setHeader(string $name, string $value = '') : void
    {
        $this->headers[$name] = $value;
    }
}
