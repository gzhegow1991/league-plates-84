<?php

declare(strict_types=1);

namespace League\Plates\Tests\Extension;

use League\Plates\Engine;
use PHPUnit\Framework\TestCase;
use League\Plates\Extension\URI;


class URITest extends TestCase
{
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new URI('/green/red/blue');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Extension\URI', $this->extension);
    }

    public function testRegister()
    {
        $engine = new Engine();
        $extension = new URI('/green/red/blue');
        $extension->register($engine);
        $this->assertTrue($engine->doesFunctionExist('uri'));
    }

    public function testGetUrl()
    {
        $this->assertTrue($this->extension->runUri() === '/green/red/blue');
    }

    public function testGetSpecifiedSegment()
    {
        $this->assertTrue($this->extension->runUri(1) === 'green');
        $this->assertTrue($this->extension->runUri(2) === 'red');
        $this->assertTrue($this->extension->runUri(3) === 'blue');
    }

    public function testSegmentMatch()
    {
        $this->assertTrue($this->extension->runUri(1, 'green'));
        $this->assertFalse($this->extension->runUri(1, 'red'));
    }

    public function testSegmentMatchSuccessResponse()
    {
        $this->assertTrue($this->extension->runUri(1, 'green', 'success') === 'success');
    }

    public function testSegmentMatchFailureResponse()
    {
        $this->assertFalse($this->extension->runUri(1, 'red', 'success'));
    }

    public function testSegmentMatchFailureCustomResponse()
    {
        $this->assertTrue($this->extension->runUri(1, 'red', 'success', 'fail') === 'fail');
    }

    public function testRegexMatch()
    {
        $this->assertTrue($this->extension->runUri('/[a-z]+/red/blue'));
    }

    public function testRegexMatchSuccessResponse()
    {
        $this->assertTrue($this->extension->runUri('/[a-z]+/red/blue', 'success') === 'success');
    }

    public function testRegexMatchFailureResponse()
    {
        $this->assertFalse($this->extension->runUri('/[0-9]+/red/blue', 'success'));
    }

    public function testRegexMatchFailureCustomResponse()
    {
        $this->assertTrue($this->extension->runUri('/[0-9]+/red/blue', 'success', 'fail') === 'fail');
    }

    public function testInvalidCall()
    {
        // Invalid use of the uri function.
        $this->expectException(\LogicException::class);

        $this->extension->runUri(array());
    }

    public function testFetchNonExistingUriIndex()
    {
        $engine = new Engine();
        $extension = new URI('/');
        $extension->register($engine);
        $this->assertTrue(is_null($extension->runUri(2)));
    }

    public function testComparehNonExistingUriIndex()
    {
        $engine = new Engine();
        $extension = new URI('/hello');
        $extension->register($engine);
        $this->assertFalse($extension->runUri(2, 'hello'));
    }
}
