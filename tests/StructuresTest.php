<?php

namespace Tests;

use Thor\Structures\Item;
use Thor\Structures\Container;
use PHPUnit\Framework\TestCase;
use Thor\Structures\ItemInterface;

final class StructuresTest extends TestCase
{

    public function testContainerCreation(): Container
    {
        $container = new Container('container1');
        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(ItemInterface::class, $container);

        return $container;
    }

    /**
     * @depends testContainerCreation
     */
    public function testAddItem(Container $container): Container
    {
        $container->setItem(new Item('item1', 'item1-value'));
        $this->assertTrue($container->hasItem('item1'));

        return $container;
    }

    /**
     * @depends testAddItem
     */
    public function testEmbeddedContainer(Container $container): Container
    {
        $container->setItem($container2 = new Container('container2'));
        $container->getItem('container2')->setItem(new Item('item2', 'item2-value'));
        $this->assertTrue($container->hasItem('container2'));
        $this->assertTrue($container2->hasItem('item2'));

        return $container;
    }

    /**
     * @depends testEmbeddedContainer
     */
    public function testCopy(Container $container): void
    {
        $container3 = new Container('container3');
        $container->copyInto($container3);
        $container->eachItem(
            fn(string $key, ?ItemInterface $item) => $this->assertEquals($item->getValue(), $container3->getItem($key)->getValue())
        );
        $this->assertEquals('item2-value', $container3->getItem('container2')->getItem('item2')->getValue());
    }

}
