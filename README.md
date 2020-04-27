# Laravel Conditional Mock Handler for Guzzle

This package offers an easy way to use the
 [Conditional Mock Handler for Guzzle](https://github.com/WebWhales/guzzle-conditional-mock-handler) package while
 writing tests in a Laravel application. The Conditional Mock Handler can be used to load mocked responses conditionally
 based on the URL, instead of a fixed queue.


## Installation

Install this package using composer:

```
composer require-dev webwhales/laravel-guzzle-conditional-mock-handler
```


## Simple Example

To use the Conditional Mock Handler, you'll have to use the `TestWithGuzzleMockHandler` trait in your test. Furthermore,
 the Guzzle client has to instantiated by either the Laravel container or Laravel's dependency injection.

See the following example:

```php
use Illuminate\Foundation\Testing\TestCase;
use WebWhales\LaravelGuzzleConditionalMockHandler\TestWithGuzzleMockHandler;

class ASimpleTest extends TestCase
{
    use TestWithGuzzleMockHandler;

    public function testSomething()
    {
        /**
         * @var \GuzzleHttp\Client $client
         */
        $client = $this->app->make(Client::class);

        // Add a mocked response
        $this->loadMockedResponseData('https://example.com', 'This is a test');

        // Get the response
        $response = $client->get('https://example.com');

		// Test the response content
        $this->assertEquals('This is a test', $response->getBody()->getContents());
    }
```

You can also use a Guzzle `Response` object (or any object implementing the `\Psr\Http\Message\ResponseInterface`
 interface), to give you more control about the response object itself:

```php
// Add a mocked response
$this->loadMockedResponse('https://example.com', new Response(400, ['X-Test' => 'Test'], 'This is a test'));

// Get the response
$response = $client->get('https://example.com');

// Test the response content
$this->assertEquals(400, $response->getStatusCode());
$this->assertEquals('This is a test', $response->getBody()->getContents());
```


## Regex example

The [Conditional Mock Handler for Guzzle](https://github.com/WebWhales/guzzle-conditional-mock-handler) package also
 supports regex patterns:

```php
// Add mocked responses
$this->loadMockedResponseData('^http(s)?://example\.', 'This is a test');

// Make a request to a matching URL
$response = $client->request('GET', 'https://example.com');

// Test the response content
$this->assertEquals('This is a test', $response->getBody()->getContents());


// Make a request to a non matching URL
// This will retrieve the actual content of https://www.example.com
$response = $client->request('GET', 'https://www.example.com');

// Test the response content
$this->assertNotEquals('This is a test', $response->getBody()->getContents());
```


## License

The this package is open source software licensed under the [MIT license](https://opensource.org/licenses/MIT)