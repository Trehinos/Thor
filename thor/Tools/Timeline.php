<?php

namespace Thor\Tools;

use Exception;
use Thor\Cli\Console\Color;
use Thor\Cli\Console\Console;
use Thor\Debug\Logger;

class Timeline
{

    private array $markers = [];

    public function __construct()
    {
        $this->mark('INIT');
    }

    /**
     * @param string|null $label if no label or null is provided, a random 12 hex digits code will be generated.
     *
     * @return $this
     */
    public function mark(?string $label = null): self
    {
        if ($label === null) {
            try {
                $label = Guid::hex(6);
            } catch (Exception $e) {
                $label = Strings::trimOrPad(dechex(rand(0, PHP_INT_MAX)), 12, '0');
                Logger::logThrowable($e);
            }
        }
        $time = microtime(true);
        $this->markers[] = [
            'label' => $label,
            'time' => $time * 1000
        ];
        return $this;
    }

    public function dumpHtml(): void
    {
        echo '<pre style="display: block; text-align: left; width: 400px; margin: 1em auto; font-family: Roboto, sans-serif;">';
        echo '<div style="margin-bottom: 1.6em; text-align: center;"><strong>&bull; TimeLine &bull;</strong></div>';

        echo $this->eachMarker(
            fn(
                $label,
                $time
            ) => "<div style='float: right'>$time ms</div>» <strong style='border-bottom: 1px dotted #bbb; display: inline-block;width: 100%;'>$label</strong><br>"
        );

        $time = $this->getTotalTime();
        echo "<br><div style='float: right'>$time ms</div>» <strong style='border-bottom: 1px dotted #bbb; display: inline-block;width: 100%;'>TOTAL</strong><br>";

        echo '</pre>';
    }

    /**
     * @param callable $codeToDump (string $label, string $time)
     *
     * @return string
     */
    private function eachMarker(callable $codeToDump): string
    {
        $str = '';
        for ($index = 1; $index < count($this->markers); $index++) {
            $label = $this->markers[$index]['label'];
            $str .= $codeToDump($label, $this->getDelta($index, $index - 1));
        }
        return $str;
    }

    private function getTotalTime(): string
    {
        return $this->getDelta(count($this->markers) - 1, 0);
    }

    private function getDelta(int $index, int $indexDiff): string
    {
        return number_format(
            $this->markers[$index]['time'] - $this->markers[$indexDiff]['time'],
            3,
            ",",
            "\xe2\x80\x89"
        );
    }

    public function dumpCli(): void
    {
        $console = new Console();
        $this->eachMarker(fn($label, $time) => $this->cliLine($console, $label, $time));
        $this->cliLine($console, 'TOTAL', $this->getTotalTime());
        $console->writeln();
    }

    private function cliLine(Console $console, string $label, string $time): string
    {
        $console->color();
        $timeStr = Strings::trimOrPad("$time ms", 16);
        $console->echoes("[", Color::FG_GREEN, $timeStr, Color::FG_GRAY, "] $label\n");
        return '';
    }

}
