<?php


namespace Yushkevichv\PDFCadReader\PDFFont;


use FontLib\Font;
use Illuminate\Support\Facades\Storage;

class PDFFont
{
    public $code;
    public $name;
    public $encoding;
    public $defaultEncoding;
    public $type;
    public $flags;
    public $composite;
    public $toUnicode;
    public $firstChar;
    public $lastChar;
    public $subType;
    public $tables = [];
    public $init = [];
    public $fontInfo;
    public $fontFile;
    public $fontFileData;
    public $CIDSystemInfo = [];

    // Unicode Private Use Areas:
    const PRIVATE_USE_AREAS = [
        [0xE000, 0xF8FF],     // BMP (0)
        [0x100000, 0x10FFFD], // PUP (16)
    ];

    // PDF Glyph Space Units are one Thousandth of a TextSpace Unit
    // except for Type 3 fonts
    const PDF_GLYPH_SPACE_UNITS = 1000;

    const FontFlags = [
        'FixedPitch' => 1,
        'Serif' => 2,
        'Symbolic' => 4,
        'Script' => 8,
        'Nonsymbolic' => 32,
        'Italic' => 64,
        'AllCap' => 65536,
        'SmallCap' => 131072,
        'ForceBold' => 262144,
    ];

    const MacStandardGlyphOrdering = [
    '.notdef', '.null', 'nonmarkingreturn', 'space', 'exclam', 'quotedbl',
    'numbersign', 'dollar', 'percent', 'ampersand', 'quotesingle', 'parenleft',
    'parenright', 'asterisk', 'plus', 'comma', 'hyphen', 'period', 'slash',
    'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight',
    'nine', 'colon', 'semicolon', 'less', 'equal', 'greater', 'question', 'at',
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'bracketleft',
    'backslash', 'bracketright', 'asciicircum', 'underscore', 'grave', 'a', 'b',
    'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q',
    'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'braceleft', 'bar', 'braceright',
    'asciitilde', 'Adieresis', 'Aring', 'Ccedilla', 'Eacute', 'Ntilde',
    'Odieresis', 'Udieresis', 'aacute', 'agrave', 'acircumflex', 'adieresis',
    'atilde', 'aring', 'ccedilla', 'eacute', 'egrave', 'ecircumflex', 'edieresis',
    'iacute', 'igrave', 'icircumflex', 'idieresis', 'ntilde', 'oacute', 'ograve',
    'ocircumflex', 'odieresis', 'otilde', 'uacute', 'ugrave', 'ucircumflex',
    'udieresis', 'dagger', 'degree', 'cent', 'sterling', 'section', 'bullet',
    'paragraph', 'germandbls', 'registered', 'copyright', 'trademark', 'acute',
    'dieresis', 'notequal', 'AE', 'Oslash', 'infinity', 'plusminus', 'lessequal',
    'greaterequal', 'yen', 'mu', 'partialdiff', 'summation', 'product', 'pi',
    'integral', 'ordfeminine', 'ordmasculine', 'Omega', 'ae', 'oslash',
    'questiondown', 'exclamdown', 'logicalnot', 'radical', 'florin',
    'approxequal', 'Delta', 'guillemotleft', 'guillemotright', 'ellipsis',
    'nonbreakingspace', 'Agrave', 'Atilde', 'Otilde', 'OE', 'oe', 'endash',
    'emdash', 'quotedblleft', 'quotedblright', 'quoteleft', 'quoteright',
    'divide', 'lozenge', 'ydieresis', 'Ydieresis', 'fraction', 'currency',
    'guilsinglleft', 'guilsinglright', 'fi', 'fl', 'daggerdbl', 'periodcentered',
    'quotesinglbase', 'quotedblbase', 'perthousand', 'Acircumflex',
    'Ecircumflex', 'Aacute', 'Edieresis', 'Egrave', 'Iacute', 'Icircumflex',
    'Idieresis', 'Igrave', 'Oacute', 'Ocircumflex', 'apple', 'Ograve', 'Uacute',
    'Ucircumflex', 'Ugrave', 'dotlessi', 'circumflex', 'tilde', 'macron',
    'breve', 'dotaccent', 'ring', 'cedilla', 'hungarumlaut', 'ogonek', 'caron',
    'Lslash', 'lslash', 'Scaron', 'scaron', 'Zcaron', 'zcaron', 'brokenbar',
    'Eth', 'eth', 'Yacute', 'yacute', 'Thorn', 'thorn', 'minus', 'multiply',
    'onesuperior', 'twosuperior', 'threesuperior', 'onehalf', 'onequarter',
    'threequarters', 'franc', 'Gbreve', 'gbreve', 'Idotaccent', 'Scedilla',
    'scedilla', 'Cacute', 'cacute', 'Ccaron', 'ccaron', 'dcroat'];

    public function buildTables()
    {
        $initCMap = $this->initCMapTable();
        $this->toUnicode = $initCMap['toUnicode'];
        $this->init['init_cmap'] = $initCMap['cmap'];

//        dd($this->getUnicodeSymbol(581));

    }

    public function initCMapTable()
    {
        if(!$this->composite) {
            return null;
        }

        $baseEncodingName = $this->getBaseEncoding();

        if($baseEncodingName) {
            $defaultEncoding = FontEncodings::getEncoding($baseEncodingName);
        } else {
            $isSymbolicFont = !!($this->flags & self::FontFlags['Symbolic']);
            $isNonSymbolicFont = !!($this->flags & self::FontFlags['Nonsymbolic']);

            // According to "Table 114" in section "9.6.6.1 General" (under
            // "9.6.6 Character Encoding") of the PDF specification, a Nonsymbolic
            // font should use the `StandardEncoding` if no encoding is specified.
            $defaultEncoding = FontEncodings::StandardEncoding;
            if ($this->type === 'TrueType' && !isNonsymbolicFont) {
                $defaultEncoding = FontEncodings::WinAnsiEncoding;
            }

            // @todo add work with symbolic font
        }

        $this->defaultEncoding = $defaultEncoding;
        $toUnicode = $this->buildToUnicode();
//        dd($toUnicode);

        return [
            'toUnicode' => $toUnicode,
            'cmap' => new CMap(false)

        ];
    }

    public function getUnicodeSymbol($i) {
        if ($this->toUnicode['firstChar'] <= $i && $i <= $this->toUnicode['lastChar']) {
            return mb_chr($i);
        }
        return null;
    }

    private function buildToUnicode()
    {
        // According to the spec if the font is a simple font we should only map
        // to unicode if the base encoding is MacRoman, MacExpert, or WinAnsi or
        // the differences array only contains adobe standard or symbol set names,
        // in pratice it seems better to always try to create a toUnicode map
        // based of the default encoding.
        if (!$this->composite /* is simple font */) {
            return $this->buildSimpleFontToUnicode();
        }

        if ($this->composite && (
                ($this->CIDSystemInfo['Registry'] === 'Adobe' &&
                    ($this->CIDSystemInfo['Ordering'] === 'GB1' ||
                    $this->CIDSystemInfo['Ordering'] === 'CNS1' ||
                    $this->CIDSystemInfo['Ordering'] === 'Japan1' ||
                    $this->CIDSystemInfo['Ordering'] === 'Korea1')
                )
            )
        ) {
            // @todo implement work with "simple" font
            throw new \Exception('work with this CIDSystem Font not implemented');
        }

        return [
            'firstChar' => $this->firstChar,
            'lastChar' => $this->lastChar,
        ];


    }

    private function buildSimpleFontToUnicode()
    {
        // @todo
        throw new \Exception('todo build simple font');
    }

    public function getBaseEncoding()
    {
        $baseEncodingName = $this->encoding;
        if (($baseEncodingName !== 'MacRomanEncoding' &&
            $baseEncodingName !== 'MacExpertEncoding' &&
            $baseEncodingName !== 'WinAnsiEncoding')) {
            $baseEncodingName = null;
        }
        return $baseEncodingName;
    }

    public function isCorrectFontTypes()
    {
        // @todo implement check from file and from descriptor
        return true;

    }

    public function buildFontFileData()
    {
        $tmpFileName = 'font_'.time().'.tiff';
        Storage::disk('local')->put($tmpFileName, $this->fontFile->stream);
        $this->fontFileData = Font::load(storage_path('app/font.tiff'));
//        $glyphIndexArray = array_flip($this->fontFileData->getUnicodeCharMap())[581];
//        dd(html_entity_decode("&#{$glyphIndexArray};"));
//        dd($this->fontFileData->getData('glyf')[581]->getSVGContours());
        Storage::disk('local')->delete($tmpFileName);
//        dd($this->fontFileData->getData('glyf')[0]->raw);
    }

    public function decode($str) :string
    {
        preg_match("#([<](.*)[>])#", $str, $parts);
        if($parts) {
           $str = $parts[2];
        }
        $glyphIndexArray = array_flip($this->fontFileData->getUnicodeCharMap());
        $chars = str_split($str, 4);
        $text = '';
        foreach ($chars as $char) {
            $charCode = hexdec($char);
            $text .= html_entity_decode("&#{$glyphIndexArray[$charCode]};");
        }

        dd($text);


    }
}
