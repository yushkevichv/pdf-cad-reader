<?php

namespace Yushkevichv\PDFCadReader\PDFObjectElement;

use Yushkevichv\PDFCadReader\Encoder;

class ElementHexa extends BaseElement
{
    public static function parse($content, &$offset = 0)
    {
        if (preg_match('/^\s*\<(?P<name>[A-F0-9]+)\>/is', $content, $match)) {
            $name = $match['name'];
            $offset += strpos($content, '<'.$name) + strlen($name) + 2; // 1 for '>'
            // repackage string as standard
            $name = '('.Encoder::decodeHexa($name).')';

            return new self($name);
//            $element = false;
//            if (!($element = ElementDate::parse($name, $document))) {
//                $element = ElementString::parse($name, $document);
//            }
//            return $element;
        }
    }
}
