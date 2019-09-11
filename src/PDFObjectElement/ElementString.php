<?php


namespace Yushkevichv\PDFCadReader\PDFObjectElement;


use Yushkevichv\PDFCadReader\Encoder;

class ElementString extends BaseElement
{

    public static function parse($content, &$offset = 0)
    {
        if (preg_match('/^\s*\((?P<name>.*)/s', $content, $match)) {
            $name = $match['name'];
            // Find next ')' not escaped.
            $cur_start_text = $start_search_end = 0;
            while (($cur_start_pos = strpos($name, ')', $start_search_end)) !== false) {
                $cur_extract = substr($name, $cur_start_text, $cur_start_pos - $cur_start_text);
                preg_match('/(?P<escape>[\\\]*)$/s', $cur_extract, $match);
                if (!(strlen($match['escape']) % 2)) {
                    break;
                }
                $start_search_end = $cur_start_pos + 1;
            }
            // Extract string.
            $name   = substr($name, 0, $cur_start_pos);
            $offset += strpos($content, '(') + $cur_start_pos + 2; // 2 for '(' and ')'
            $name   = str_replace(
                ['\\\\', '\\ ', '\\/', '\(', '\)', '\n', '\r', '\t'],
                ['\\',   ' ',   '/',   '(',  ')',  "\n", "\r", "\t"],
                $name
            );
            // Decode string.
//            $name = Encoder::decodeOctal($name);
//            $name = Encoder::decodeEntities($name);
//            $name = Encoder::decodeHexadecimal($name, false);
            $name = Encoder::decodeUnicode($name);
            return new self($name);
        }
        return null;
    }

}
