<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Framework\Security\DbUser;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Structures\Collection\Collection;
use Thor\Database\PdoExtension\PdoHandler;
use Thor\Database\PdoExtension\PdoRequester;

final class CollectionTest extends TestCase
{

    public function testCollection(): void
    {
        $collection = new Collection();
        $this->assertEmpty($collection);

        $collection->push('test', 'test2', 1);
        $collection[] = 'sgvsvsd';
        $this->assertTrue($collection->keyExists(3));
        $this->assertNotEmpty($collection);
        $this->assertEquals('test2', $collection[1]);
        $this->assertTrue($collection->isList());

        $crud = new CrudHelper(DbUser::class, new PdoRequester(new PdoHandler('dsn')));

    }

}
