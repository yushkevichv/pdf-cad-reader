<?php


namespace Yushkevichv\PDFCadReader\PDFObjectElement;


class ElementXRef extends BaseElement
{

    public static function parse($content,  &$offset = 0)
    {
        if (preg_match('/^\s*(?P<id>[0-9]+\s+[0-9]+\s+R)/s', $content, $match)) {
            $id = $match['id'];
            $offset += strpos($content, $id) + strlen($id);
            $id = str_replace(' ', '_', rtrim($id, ' R'));
            return new self($id);
        }
        return false;
    }

}
