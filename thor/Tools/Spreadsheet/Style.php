<?php

namespace Thor\Tools\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;

/**
 *
 */

/**
 *
 */
final class Style
{

    private static array $colors = [];

    /**
     * @param Font|null    $font
     * @param Fill|null    $fill
     * @param Borders|null $borders
     */
    public function __construct(
        public readonly ?Font $font = null,
        public readonly ?Fill $fill = null,
        public readonly ?Borders $borders = null,
    ) {
    }

    /**
     * @param string $name
     *
     * @return Color
     */
    public static function color(string $name): Color
    {
        if (empty(self::$colors)) {
            self::$colors ??= [
                'white' => new Color(Color::COLOR_WHITE),
                'black' => new Color(Color::COLOR_BLACK),
            ];
        }

        return self::$colors[$name] ?? self::$colors['white'];
    }

    /**
     * @param string $name
     * @param Color  $color
     *
     * @return void
     */
    public static function setColor(string $name, Color $color): void
    {
        self::$colors[$name] = $color;
    }

    /**
     * @param Cell $cell
     *
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function apply(Cell $cell): void
    {
        $style = $cell->getStyle();
        if (null !== $this->font) {
            $style->setFont($this->font);
        }
        if (null !== $this->fill) {
            $style->getFill()
                  ->setFillType($this->fill->getFillType())
                  ->setStartColor($this->fill->getStartColor())
                  ->setEndColor($this->fill->getEndColor());
        }
        if (null !== $this->borders) {
            $style->getBorders()->applyFromArray($this->borders->getAllBorders()->exportArray());
        }
    }

    /**
     * @param string      $fontName
     * @param int         $size
     * @param Color|null  $fontColor
     * @param string      $fillType
     * @param Color|null  $fillStartColor
     * @param Color|null  $fillEndColor
     * @param Border|null $borderTop
     * @param Border|null $borderRight
     * @param Border|null $borderBottom
     * @param Border|null $borderLeft
     *
     * @return static
     */
    public static function createStyle(
        string $fontName = 'Calibri',
        int $size = 10,
        ?Color $fontColor = null,
        string $fillType = Fill::FILL_SOLID,
        ?Color $fillStartColor = null,
        ?Color $fillEndColor = null,
        ?Border $borderTop = null,
        ?Border $borderRight = null,
        ?Border $borderBottom = null,
        ?Border $borderLeft = null,
    ): self {
        return new self(
            (new Font())
                ->setName($fontName)
                ->setSize($size)
                ->setColor($fontColor ?? self::color('black')),
            (new Fill())
                ->setFillType($fillType)
                ->setStartColor($fillStartColor ?? self::color('white'))
                ->setEndColor($fillEndColor ?? self::color('white')),
            (new Borders())->applyFromArray([
                'top'    => $borderTop,
                'right'  => $borderRight,
                'bottom' => $borderBottom,
                'left'   => $borderLeft,
            ])
        );
    }

}
