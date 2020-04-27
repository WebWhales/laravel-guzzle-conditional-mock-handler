<?php
/**
 * Laravel Guzzle Conditional Mock Handler
 *
 * @author    Ronald Edelschaap (Web Whales) <ronald.edelschaap@webwhales.nl>
 * @copyright 2020 Web Whales (https://webwhales.nl)
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/WebWhales/laravel-guzzle-conditional-mock-handler
 */

namespace WebWhales\LaravelGuzzleConditionalMockHandler;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WebWhales\GuzzleConditionalMockHandler\Handler as MockHandler;

/**
 * Trait UsesGuzzleMockHandler
 *
 * @package WebWhales\LaravelGuzzleConditionalMockHandler
 *
 * @property-read \Illuminate\Contracts\Foundation\Application $app
 */
trait TestWithGuzzleMockHandler
{
    /**
     * @var \WebWhales\GuzzleConditionalMockHandler\Handler
     */
    private $guzzleMockHandler;

    /**
     * Get the Guzzle Mock Handler.
     *
     * @return \WebWhales\GuzzleConditionalMockHandler\Handler
     */
    protected function getGuzzleMockHandler(): MockHandler
    {
        if (! $this->guzzleMockHandler) {
            $this->setUpGuzzleMockHandler();
        }

        // Return the handler so a new response can be added to it
        return $this->guzzleMockHandler;
    }

    /**
     * Set up the Guzzle Mock Handler for testing. This will inject the Mock Handler to any Guzzle Client instantiated through the Laravel container.
     *
     * @return self
     */
    protected function setUpGuzzleMockHandler(): self
    {
        $this->guzzleMockHandler = new MockHandler;

        // Create a handler stack to mock the Guzzle Client with
        $handler = HandlerStack::create($this->guzzleMockHandler);
        $client  = function (\Illuminate\Container\Container $container, array ...$config) use ($handler) {
            // Pass the original options with our custom handler to a new Guzzle Client instance
            $config = isset($config[0]) ? $config[0] : $config;
            $config = \array_merge($config, ['handler' => $handler]);

            return new Client($config);
        };

        // Bind the Guzzle Client instance to the mock
        $this->app->bind(Client::class, $client);

        return $this;
    }

    /**
     * Load a mocked Response object for the given URL (pattern).
     *
     * @param string            $url      The URL to mock. Can be a absolute URL or a regex pattern.
     * @param ResponseInterface $response The mocked response.
     *
     * @return self
     */
    protected function loadMockedResponse(string $url, ResponseInterface $response): self
    {
        $this->getGuzzleMockHandler()->addResponse($url, $response);

        return $this;
    }

    /**
     * Load mocked response data for the given URL (pattern).
     *
     * @param string                                     $url                The URL to mock. Can be a absolute URL or a regex pattern.
     * @param string|null|array|resource|StreamInterface $response           The mocked response body. Can be a Response object or body input for a Response object.
     * @param int                                        $responseStatusCode The HTTP response status code.
     * @param array                                      $responseHeaders    The response headers.
     *
     * @return self
     */
    protected function loadMockedResponseData(
        string $url,
        $response,
        int $responseStatusCode = 200,
        array $responseHeaders = []
    ): self {
        if (\is_array($response)) {
            $response = \json_encode($response);
        }

        if (! $response instanceof ResponseInterface) {
            $response = new Response($responseStatusCode, $responseHeaders, $response);
        }

        return $this->loadMockedResponse($url, $response);
    }

    /**
     * Unload mocked responses for the given URL (pattern).
     *
     * @param string $url The URL. Can be a absolute URL or a regex pattern.
     *
     * @return self
     */
    protected function unloadMockedResponse(string $url): self
    {
        $this->getGuzzleMockHandler()->removeResponse($url);

        return $this;
    }

    /**
     * Unload mocked responses for all URLs. This function basically resets the mocked responses
     *
     * @return self
     */
    protected function unloadMockedResponses(): self
    {
        $this->getGuzzleMockHandler()->resetResponses();

        return $this;
    }
}