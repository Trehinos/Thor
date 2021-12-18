<?php

namespace Tests;

use Thor\Cache\Cache;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{

    public function testCache(): Cache
    {
        $cache = new Cache();
        $this->assertInstanceOf(Cache::class, $cache);
        $this->assertEmpty($cache->getValue());

        return $cache;
    }

    /**
     * @depends testCache
     */
    public function testAddItem(Cache $cache): Cache
    {
        $this->assertTrue($cache->set('test', 'testValue'));

        return $cache;
    }

    /**
     * @depends testAddItem
     */
    public function testGetItem(Cache $cache): Cache
    {
        $this->assertNotEmpty($cache->getValue());
        $this->assertTrue($cache->has('test'));
        $this->assertSame('testValue', $cache->get('test'));

        return $cache;
    }

    /**
     * @depends testGetItem
     */
    public function testClear(Cache $cache): void
    {
        $cache->clear();
        $this->assertEmpty($cache->getValue());
    }

}
