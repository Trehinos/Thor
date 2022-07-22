<?php

namespace Tests;

use Thor\Tools\Email;
use Thor\Tools\Strings;
use Thor\Tools\DateTimes;
use PHPUnit\Framework\TestCase;
use Thor\Tools\PlaceholderFormat;

final class ToolsTest extends TestCase
{

    public function testDateTimes(): void
    {
        $subjectDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-12-31 14:30:00');
        $now = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-12-31 15:00:00');
        $now12hAfter = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-01-01 02:30:00');
        $now1DayAfter = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-01-01 15:00:00');

        $string1 = DateTimes::getRelativeDateTime($subjectDateTime, relativeTo: $now);
        $string2 = DateTimes::getRelativeDateTime($subjectDateTime, relativeTo: $now12hAfter);
        $string3 = DateTimes::getRelativeDateTime($subjectDateTime, relativeTo: $now1DayAfter);
        $this->assertSame('14:30', $string1);
        $this->assertSame('Yesterday 14:30', $string2);
        $this->assertSame('2021-12-31', $string3);
    }

    public function testStringSplit(): void
    {
        $stringToSplit = 'a string to split';
        $head = '';

        $stringToSplit = Strings::split($stringToSplit, ' ', $head);
        $this->assertSame('a', $head);
        $this->assertSame('string to split', $stringToSplit);

        $stringToSplit = Strings::split($stringToSplit, ' ', $head);
        $this->assertSame('string', $head);
        $this->assertSame('to split', $stringToSplit);

        $stringToSplit = Strings::split($stringToSplit, ' ', $head);
        $this->assertSame('to', $head);
        $this->assertSame('split', $stringToSplit);

        $stringToSplit = Strings::split($stringToSplit, ' ', $head);
        $this->assertSame('split', $head);
        $this->assertSame('', $stringToSplit);

        $stringToSplit = Strings::split($stringToSplit, ' ', $head);
        $this->assertSame('', $head);
        $this->assertSame('', $stringToSplit);
    }

    public function testInterpolate(): void
    {
        $date = new \DateTimeImmutable();
        $context = ['date' => $date->format('Y-m-d')];
        $expected = "Today's date is {$date->format('Y-m-d')}.";
        $string = 'Today\'s date is {date}.';
        $stringPhpStyle = 'Today\'s date is $date.';

        $this->assertSame($expected, Strings::interpolate($string, $context));
        $this->assertSame($expected, Strings::interpolate($stringPhpStyle, $context, PlaceholderFormat::SIGIL));
    }

    public function testPrefixAndSuffix(): void
    {
        $this->assertSame('', Strings::prefix('prefix', null));
        $this->assertSame('', Strings::suffix(null, 'suffix'));

        $this->assertSame('', Strings::prefix('prefix', ''));
        $this->assertSame('', Strings::suffix('', 'suffix'));

        $this->assertSame('prefixed', Strings::prefix('prefix', 'ed'));
        $this->assertSame('the suffix', Strings::suffix('the ', 'suffix'));
    }

    public function testEmail(): void
    {
        $email = new Email('noreply@trehinos.eu', 'Test', 'Ceci est un <strong>test</strong>', type: Email::EMAIL_MULTIPART);
        $email->to('user@example.com');
        $r = $email->send();

        $this->assertTrue($r);
    }

}
