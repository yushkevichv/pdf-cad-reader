<?php

namespace Yushkevichv\PDFCadReader\PDFObjectElement;

class ElementNumeric extends BaseElement
{
    public static function parse($content, &$offset = 0)
    {
        if (preg_match('/^\s*(?P<value>\-?[0-9\.]+)/s', $content, $match)) {
            $value = $match['value'];
            $offset += strpos($content, $value) + strlen($value);

            return $value;
//            return new self($value);
        }
    }
}
