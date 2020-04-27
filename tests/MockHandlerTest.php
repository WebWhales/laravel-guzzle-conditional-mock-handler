<?php
/**
 * Laravel Guzzle Conditional Mock Handler
 *
 * @author    Ronald Edelschaap (Web Whales) <ronald.edelschaap@webwhales.nl>
 * @copyright 2020 Web Whales (https://webwhales.nl)
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/WebWhales/laravel-guzzle-conditional-mock-handler
 */

namespace WebWhales\LaravelGuzzleConditionalMockHandler\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use WebWhales\GuzzleConditionalMockHandler\Handler as MockHandler;
use WebWhales\LaravelGuzzleConditionalMockHandler\Tests\Fixtures\App\Foo\Bar;
use WebWhales\LaravelGuzzleConditionalMockHandler\TestWithGuzzleMockHandler;

/**
 * Class MockHandlerTest
 *
 * @package WebWhales\LaravelGuzzleConditionalMockHandler\Tests
 */
class MockHandlerTest extends TestCase
{
    use TestWithGuzzleMockHandler;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpGuzzleMockHandler();
    }

    /**
     * @test
     *
     * @covers \WebWhales\LaravelGuzzleConditionalMockHandler\TestWithGuzzleMockHandler::getGuzzleMockHandler
     */
    public function Loads_LaravelGuzzleConditionalMockHandler()
    {
        /**
         * @var \GuzzleHttp\Client       $client
         * @var \GuzzleHttp\HandlerStack $handlerStack
         */
        $client       = $this->app->make(Client::class);
        $handlerStack = $client->getConfig('handler');

        // Test whether our handler class is within the handler stack
        $this->assertStringContainsString(\ltrim(MockHandler::class, '\\'), \var_export($handlerStack, true));
    }

    /**
     * @test
     *
     * @covers \WebWhales\LaravelGuzzleConditionalMockHandler\TestWithGuzzleMockHandler::loadMockedResponse
     */
    public function Returns_MockedResponse_WhenUsing_LaravelGuzzleConditionalMockHandler()
    {
        /**
         * Prepare the test
         *
         * @var \GuzzleHttp\Client $client
         */
        $client = $this->app->make(Client::class, ['http_errors' => false]);

        // Add mocked responses
        $this->loadMockedResponse('https://example.com', new Response(400, ['X-Test' => 'Test'], 'This is a test'));


        /*
         * Perform the test
         */
        $response = $client->get('https://example.com');


        /*
         * Make the assertions
         */
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['Test'], $response->getHeader('X-Test'));
        $this->assertEquals('This is a test', $response->getBody()->getContents());
    }

    /**
     * @test
     *
     * @covers \WebWhales\LaravelGuzzleConditionalMockHandler\TestWithGuzzleMockHandler::loadMockedResponseData
     */
    public function Returns_MockedResponse_WhenUsing_LaravelGuzzleConditionalMockHandler_With_HelperFunctions()
    {
        /**
         * Prepare the test
         *
         * @var \GuzzleHttp\Client $client
         */
        $client = $this->app->make(Client::class, ['http_errors' => false]);

        // Add mocked responses
        $this->loadMockedResponseData('https://example.com/array', ['This is a test'])
             ->loadMockedResponseData('https://example.com/error', 'This is a test', 400)
             ->loadMockedResponseData('https://example.com/headers', 'This is a test', 200, ['X-Test' => 'Test'])
             ->loadMockedResponseData('https://example.com/stream', (function () {
                     $stream = \fopen('php://temp', 'w+');

                     \fwrite($stream, 'This is a test');
                     \fseek($stream, 0);

                     return $stream;
                 })()
             )
             ->loadMockedResponseData('https://example.com/string', 'This is a test')
             ->loadMockedResponseData('https://example.com/null', null);


        /*
         * Perform the tests
         */
        $this->assertEquals('["This is a test"]', $client->get('https://example.com/array')->getBody()->getContents());
        $this->assertEquals(400, $client->get('https://example.com/error')->getStatusCode());
        $this->assertEquals(['Test'], $client->get('https://example.com/headers')->getHeader('X-Test'));
        $this->assertEquals('This is a test', $client->get('https://example.com/error')->getBody()->getContents());
        $this->assertEquals('This is a test', $client->get('https://example.com/string')->getBody()->getContents());
        $this->assertEquals('', $client->get('https://example.com/null')->getBody()->getContents());
        $this->assertEquals('This is a test', $client->get('https://example.com/stream')->getBody()->getContents());
    }

    /**
     * @test
     */
    public function Returns_MockedResponse_WhenUsing_LaravelGuzzleConditionalMockHandler_Through_AnApplicationClass()
    {
        /**
         * Prepare the test
         *
         * Instantiate the class, making use of Laravel's dependency injection
         *
         * @var Bar $bar
         */
        $bar = $this->app->make(Bar::class);

        // Add mocked responses
        $this->loadMockedResponseData('https://example.com', 'This is a test');


        /*
         * Perform the test
         */
        $this->assertEquals('This is a test', $bar->doRequest('https://example.com'));
    }
}