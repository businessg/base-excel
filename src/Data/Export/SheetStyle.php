<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;

class SheetStyle extends BaseObject
{
    public const GRIDLINES_HIDE_ALL = 0;
    public const GRIDLINES_SHOW_SCREEN = 1;
    public const GRIDLINES_SHOW_PRINT = 2;
    public const GRIDLINES_SHOW_ALL = 3;

    public ?int $gridline = null;
    public ?int $zoom = null;
    public bool $hide = false;
    public bool $isFirst = false;

    public function setGridline(int $gridline): self
    {
        $this->gridline = $gridline;
        return $this;
    }

    public function setZoom(int $zoom): self
    {
        $this->zoom = $zoom;
        return $this;
    }

    public function setHide(bool $isHide): self
    {
        $this->hide = $isHide;
        return $this;
    }

    public function setIsFirst(bool $isFirst): self
    {
        $this->isFirst = $isFirst;
        return $this;
    }
}
