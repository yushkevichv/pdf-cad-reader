<?php


namespace Yushkevichv\PDFCadReader\PDFFont;


class IdentityCMap extends CMap
{

    public $vertical;

    public function __construct(bool $vertical, $num)
    {
        parent::__construct();

        $this->vertical = $vertical;
    }
}
