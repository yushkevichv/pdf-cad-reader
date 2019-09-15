<?php


namespace Yushkevichv\PDFCadReader\PDFFont;


class FontInfo
{
    public $fontFamily;
    public $fontWeight;
    public $FontBBox = [];
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
        $this->leading = $descriptor['Leading'];
        $this->capHeight = $descriptor['CapHeight'];
    }
}
