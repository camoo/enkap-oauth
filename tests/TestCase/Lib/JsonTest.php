<?php

namespace Enkap\OAuth\Test\TestCase\Lib;

use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Lib\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Json::class)]
#[Group('Lib')]
class JsonTest extends TestCase
{
    private Json $json;

    protected function setUp(): void
    {
        parent::setUp();
        $this->json = new Json();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->json);
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(Json::class, $this->json);
    }

    public function testDecodeWithNullThrowsException()
    {
        $this->expectException(EnkapException::class);
        $this->json->decode();
    }

    public function testDecodeWithInvalidStringThrowsException()
    {
        $this->expectException(EnkapException::class);
        $this->json->decode('{');
    }

    public function testCanDecode()
    {
        $this->assertEquals([], $this->json->decode(''));
        $this->assertInstanceOf(stdClass::class, $this->json->decode('{"test" : "ok"}', false));
    }

    public function testReadWithNullThrowsException()
    {
        $this->expectException(EnkapException::class);
        $this->json->read();
    }

    public function testCanRead()
    {
        $file = sys_get_temp_dir() . '/test.json';
        touch($file);
        $result = $this->json->read($file);
        $this->assertEquals([], $result);
        unlink($file);
    }
}
