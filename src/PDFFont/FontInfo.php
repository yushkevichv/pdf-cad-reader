<?php

namespace Yushkevichv\PDFCadReader\PDFFont;

class FontInfo
{
    public $fontFamily;
    public $fontWeight;
    public $fontBBox = [];
    public $ascent;
    public $descent;
    public $leading;
    public $capHeight;

    public function __construct(array $descriptor)
    {
        $this->fontFamily = $descriptor['FontFamily'];
        $this->fontWeight = $descriptor['FontWeight'];
        $this->fontBBox = $descriptor['FontBBox'];
        $this->ascent = $descriptor['Ascent'];
        $this->descent = $descriptor['Descent'];
        $this->leading = $descriptor['Leading'] ?? null;
        $this->capHeight = $descriptor['CapHeight'];
    }
}
