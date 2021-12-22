<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Validation\Filters\RegexFilter;

final class ValidationTest extends TestCase
{

    public function testRegexpFilter(): void
    {
        $contains4Digits = new RegexFilter('/\\d{4}/');
        $this->assertSame('1234', $contains4Digits->filter('1234'));
        $this->assertSame('', $contains4Digits->filter('a string'));
        $this->assertSame('a string with 1234', $contains4Digits->filter('a string with 1234'));
    }

}
