<?php

namespace Tests;

use Thor\Html\HtmlTag;
use PHPUnit\Framework\TestCase;

final class HtmlTest extends TestCase
{


    /**
     * @dataProvider htmlProvider
     */
    public function testHtmlTag(string $tag, bool $autoclose, array $attrs, string $id, string $html): void
    {
        $div = new HtmlTag($tag, $autoclose, $attrs, $id);
        $this->assertEquals($html, $div->toHtml());
    }

    public function htmlProvider(): array
    {
        return [
            'simple div tag' => ['div', false, [],  'test', '<div id="test"></div>'],
            'div with class' => ['div', false, ['class' => 'test'],  'test', '<div class="test" id="test"></div>'],
            'input' => ['input', true, ['name' => 'test', 'type' => 'text'],  'test', '<input name="test" type="text" id="test">'],
        ];
    }

}
