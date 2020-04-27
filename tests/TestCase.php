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

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package WebWhales\LaravelGuzzleConditionalMockHandler\Tests
 */
class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        return new \Illuminate\Foundation\Application();
    }
}