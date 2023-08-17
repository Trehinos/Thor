<?php

namespace Thor\Tools\Biology\NDA;

use ValueError;

enum Nucleotide: string
{

    case O = '0';

    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';

    case R = 'R';
    case S = 'S';
    case T = 'T';
    case U = 'U';

    case X = 'X';
    case Y = 'Y';
    case Z = 'Z';

    public function toInt(): int
    {
        return match ($this) {
            self::O => 0,
            self::A => 1,
            self::B => 2,
            self::C => 3,
            self::D => 4,
            self::E => 5,
            self::F => 6,
            self::G => 7,
            self::H => 8,
            self::R => 9,
            self::S => 0xA,
            self::T => 0xB,
            self::U => 0xC,
            self::X => 0xD,
            self::Y => 0xE,
            self::Z => 0xF,
        };
    }

    public static function nucleotidesFromString(string $nda): array
    {
        $output = [];
        for ($i = 0; $char = substr($nda, $i, 1), $i < strlen($nda); $i++) {
            $n = self::tryFrom($char);
            if ($n === null) {
                throw new ValueError("'$char' is an invalid value of Nucleotide...");
            }
            $output[] = $n;
        }
        return $output;
    }


    public static function stringToInt(string $codon): int
    {
        $r = 0;
        $position = 0;
        foreach (str_split($codon) as $char) {
            $r *= (self::tryFrom($char))?->toInt() * pow($position, 16);
        }
        return $r;
    }

}
