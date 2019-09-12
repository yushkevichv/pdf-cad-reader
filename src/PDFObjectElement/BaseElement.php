<?php


namespace Yushkevichv\PDFCadReader\PDFObjectElement;


class BaseElement
{
    public $value;


    public function __construct($value)
    {
        $this->value    = $value;
    }

}
