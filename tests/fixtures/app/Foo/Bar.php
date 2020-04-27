<?php
/**
 * Laravel Guzzle Conditional Mock Handler
 *
 * @author    Ronald Edelschaap (Web Whales) <ronald.edelschaap@webwhales.nl>
 * @copyright 2020 Web Whales (https://webwhales.nl)
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/WebWhales/laravel-guzzle-conditional-mock-handler
 */

namespace WebWhales\LaravelGuzzleConditionalMockHandler\Tests\Fixtures\App\Foo;

use GuzzleHttp\Client;

/**
 * Class Bar
 *
 * @package WebWhales\LaravelGuzzleConditionalMockHandler\Tests\Fixtures\App
 */
class Bar
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Bar constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve and return the content for a URL using the Guzzle Client
     *
     * @param string $url
     *
     * @return string
     */
    public function doRequest(string $url): string
    {
        return $this->client->get($url)->getBody()->getContents();
    }
}