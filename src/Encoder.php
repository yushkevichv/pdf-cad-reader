<?php


namespace Yushkevichv\PDFCadReader;


class Encoder
{

    public static function decodeEntities($text)
    {
        $parts = preg_split('/(#\d{2})/s', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $text  = '';
        foreach ($parts as $part) {
            if (preg_match('/^#\d{2}$/', $part)) {
                $text .= chr(hexdec(trim($part, '#')));
            } else {
                $text .= $part;
            }
        }
        return $text;
    }

    public static function decodeHexa($value)
    {
        $text   = '';
        $length = strlen($value);
        if (substr($value, 0, 2) == '00') {
            for ($i = 0; $i < $length; $i += 4) {
                $hex = substr($value, $i, 4);
                $text .= '&#' . str_pad(hexdec($hex), 4, '0', STR_PAD_LEFT) . ';';
            }
        } else {
            for ($i = 0; $i < $length; $i += 2) {
                $hex = substr($value, $i, 2);
                $text .= chr(hexdec($hex));
            }
        }
        $text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function decodeOctal($text)
    {
        $parts = preg_split('/(\\\\\d{3})/s', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $text  = '';
        foreach ($parts as $part) {
            if (preg_match('/^\\\\\d{3}$/', $part)) {
                $text .= chr(octdec(trim($part, '\\')));
            } else {
                $text .= $part;
            }
        }
        return $text;
    }

    /**
     * @param string $hexa
     * @param bool   $add_braces
     *
     * @return string
     */
    public static function decodeHexadecimal($hexa, $add_braces = false)
    {
        // Special shortcut for XML content.
        if (stripos($hexa, '<?xml') !== false) {
            return $hexa;
        }
        $text  = '';
        $parts = preg_split('/(<[a-f0-9]+>)/si', $hexa, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $part) {
            if (preg_match('/^<.*>$/', $part) && stripos($part, '<?xml') === false) {
                $part = trim($part, '<>');
                if ($add_braces) {
                    $text .= '(';
                }
                $part = pack('H*', $part);
                $text .= ($add_braces ? preg_replace('/\\\/s', '\\\\\\', $part) : $part);
                if ($add_braces) {
                    $text .= ')';
                }
            } else {
                $text .= $part;
            }
        }
        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function decodeUnicode($text)
    {
        if (preg_match('/^\xFE\xFF/i', $text)) {
            // Strip U+FEFF byte order marker.
            $decode = substr($text, 2);
            $text   = '';
            $length = strlen($decode);
            for ($i = 0; $i < $length; $i += 2) {
                $text .= self::uchr(hexdec(bin2hex(substr($decode, $i, 2))));
            }
        }
        return $text;
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public static function uchr($code)
    {
        return html_entity_decode('&#' . ((int)$code) . ';', ENT_NOQUOTES, 'UTF-8');
    }

}
