<?php

namespace Yushkevichv\PDFCadReader\PDFObjectElement;

use Yushkevichv\PDFCadReader\Encoder;
use Yushkevichv\PDFCadReader\PDFObject;

class ElementName extends BaseElement
{
    public static function parse($content,  &$offset = 0)
    {
        if (preg_match('/^\s*\/(?P<name>[A-Z0-9\-\+,#\.]+)/is', $content, $match)) {
            $name   = $match['name'];
            $offset += strpos($content, $name) + strlen($name);
            $name   = Encoder::decodeEntities($name);
            return new self($name);
        }
        return null;
    }

}
