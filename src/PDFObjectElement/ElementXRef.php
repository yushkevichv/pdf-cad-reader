<?php

namespace Yushkevichv\PDFCadReader\PDFObjectElement;

class ElementXRef extends BaseElement
{
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
