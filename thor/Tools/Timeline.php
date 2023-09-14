<?php

namespace Thor\Tools;

class Timeline
{

    private array $times = [];

    public function __construct()
    {
        $this->add('INIT');
    }

    public function add(?string $label = null): self
    {
        if ($label === null) {
            $label = Guid::hex(6);
        }
        $time = microtime(true);
        $this->times[] = [
            'label' => $label,
            'time'  => $time * 1000
        ];
        return $this;
    }

    public function dump()
    {
        echo '<pre style="display: block; text-align: left; width: 400px; margin: 1em auto; font-family: Roboto;">';
        echo '<div style="margin-bottom: 1.6em; text-align: center;"><strong>&bull; TimeLine &bull;</strong></div>';

        for ($index = 1; $index < count($this->times); $index++) {
            $label = $this->times[$index]['label'];
            $time  = number_format(
                $this->times[$index]['time'] - $this->times[$index - 1]['time'],
                3,
                ",",
                "&thinsp;"
            );
            echo "<div style='float: right'>$time ms</div>» <strong style='border-bottom: 1px dotted #bbb; display: inline-block;width: 100%;'>$label</strong><br>";
        }

        $time = number_format(
            $this->times[count($this->times) - 1]['time'] - $this->times[0]['time'],
            3,
            ',',
            "&thinsp;"
        );
        echo "<br><div style='float: right'>$time ms</div>» <strong style='border-bottom: 1px dotted #bbb; display: inline-block;width: 100%;'>TOTAL</strong><br>";

        echo '</pre>';
    }

}