<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;

class Style extends BaseObject
{
    public const FORMAT_ALIGN_LEFT = 1;
    public const FORMAT_ALIGN_CENTER = 2;
    public const FORMAT_ALIGN_RIGHT = 3;
    public const FORMAT_ALIGN_FILL = 4;
    public const FORMAT_ALIGN_JUSTIFY = 5;
    public const FORMAT_ALIGN_CENTER_ACROSS = 6;
    public const FORMAT_ALIGN_DISTRIBUTED = 7;
    public const FORMAT_ALIGN_VERTICAL_TOP = 8;
    public const FORMAT_ALIGN_VERTICAL_BOTTOM = 9;
    public const FORMAT_ALIGN_VERTICAL_CENTER = 10;
    public const FORMAT_ALIGN_VERTICAL_JUSTIFY = 11;
    public const FORMAT_ALIGN_VERTICAL_DISTRIBUTED = 12;

    public const UNDERLINE_SINGLE = 1;
    public const UNDERLINE_DOUBLE = 2;
    public const UNDERLINE_SINGLE_ACCOUNTING = 3;
    public const UNDERLINE_DOUBLE_ACCOUNTING = 4;

    public const BORDER_THIN = 1;
    public const BORDER_MEDIUM = 2;
    public const BORDER_DASHED = 3;
    public const BORDER_DOTTED = 4;
    public const BORDER_THICK = 5;
    public const BORDER_DOUBLE = 6;
    public const BORDER_HAIR = 7;
    public const BORDER_MEDIUM_DASHED = 8;
    public const BORDER_DASH_DOT = 9;
    public const BORDER_MEDIUM_DASH_DOT = 10;
    public const BORDER_DASH_DOT_DOT = 11;
    public const BORDER_MEDIUM_DASH_DOT_DOT = 12;
    public const BORDER_SLANT_DASH_DOT = 13;

    public const PATTERN_NONE = 1;
    public const PATTERN_SOLID = 2;
    public const PATTERN_MEDIUM_GRAY = 3;
    public const PATTERN_DARK_GRAY = 4;
    public const PATTERN_LIGHT_GRAY = 5;
    public const PATTERN_DARK_HORIZONTAL = 6;
    public const PATTERN_DARK_VERTICAL = 7;
    public const PATTERN_DARK_DOWN = 8;
    public const PATTERN_DARK_UP = 9;
    public const PATTERN_DARK_GRID = 10;
    public const PATTERN_DARK_TRELLIS = 11;
    public const PATTERN_LIGHT_HORIZONTAL = 12;
    public const PATTERN_LIGHT_VERTICAL = 13;
    public const PATTERN_LIGHT_DOWN = 14;
    public const PATTERN_LIGHT_UP = 15;
    public const PATTERN_LIGHT_GRID = 16;
    public const PATTERN_LIGHT_TRELLIS = 17;
    public const PATTERN_GRAY_125 = 18;
    public const PATTERN_GRAY_0625 = 19;

    public bool $italic = false;
    public array $align = [];
    public bool $strikeout = false;
    public int $underline = 0;
    public bool $wrap = false;
    public int $fontColor = 0;
    public float $fontSize = 0;
    public bool $bold = false;
    public int $border = 0;
    public int $backgroundColor = 0;
    public int $backgroundStyle = 0;
    public string $font = '';

    public function setItalic(bool $italic): self
    {
        $this->italic = $italic;
        return $this;
    }

    public function setAlign(array $align): self
    {
        $this->align = $align;
        return $this;
    }

    public function setStrikeout(bool $strikeout): self
    {
        $this->strikeout = $strikeout;
        return $this;
    }

    public function setUnderline(int $underline): self
    {
        $this->underline = $underline;
        return $this;
    }

    public function setWrap(bool $wrap): self
    {
        $this->wrap = $wrap;
        return $this;
    }

    public function setFontColor(int $fontColor): self
    {
        $this->fontColor = $fontColor;
        return $this;
    }

    public function setFontSize(float $fontSize): self
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    public function setBold(bool $bold): self
    {
        $this->bold = $bold;
        return $this;
    }

    public function setBorder(int $border): self
    {
        $this->border = $border;
        return $this;
    }

    public function setBackgroundColor(int $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    public function setBackgroundStyle(int $backgroundStyle): self
    {
        $this->backgroundStyle = $backgroundStyle;
        return $this;
    }

    public function setFont(string $font): self
    {
        $this->font = $font;
        return $this;
    }
}
