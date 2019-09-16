<?php
namespace Yushkevichv\PDFCadReader\PDFFont;

use FontLib\Font;

class PDFFont
{
    public $code;
    public $name;
    public $encoding;
    public $type;
    public $flags;
    public $composite;
    public $subType;
    public $fontInfo;
    public $fontFile;
    public $glyphIndexArray = [];
    public $CIDSystemInfo = [];

    public function buildFontFileData()
    {
        $tmpFileName = 'font_'.$this->code.'_'.time().'.tiff';
        $dirPath = __DIR__.'/../../tmp_storage/';

        if(!file_exists($dirPath)) {
            mkdir($dirPath, 0777);
        }

        file_put_contents($dirPath.$tmpFileName, $this->fontFile->stream);
        $fontFileData = Font::load($dirPath.$tmpFileName);
        if($fontFileData) {
            $this->glyphIndexArray = array_flip($fontFileData->getUnicodeCharMap());
        }
        unlink($dirPath.$tmpFileName);

        unset($this->fontFile);
    }

    public function decode($str) :string
    {
        preg_match("#([<](.*)[>])#", $str, $parts);
        if($parts) {
           $str = $parts[2];
        }

        $glyphIndexArray = $this->glyphIndexArray;
        if(!$glyphIndexArray) {
            // @todo check implementation for font without addditional glyphs
            return $str;
        }

        $chars = str_split($str, 4);
        $text = '';
        foreach ($chars as $char) {
            $charCode = hexdec($char);
            $text .= html_entity_decode("&#{$glyphIndexArray[$charCode]};") ?? '';
        }

        return $text;
    }
}
