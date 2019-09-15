<?php


namespace Yushkevichv\PDFCadReader\PDFFont;


class PDFFont
{
    public $code;
    public $name;
    public $encoding;
    public $defaultEncoding;
    public $type;
    public $flags;
    public $composite;
    public $firstChar;
    public $lastChar;
    public $subType;
    public $tables = [];
    public $fontInfo;
    public $fontFile;
    public $CIDSystemInfo = [];

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

    public function buildTables()
    {
        $this->tables['cmap'] = $this->buildCMapTable();

    }

    public function buildCMapTable()
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
        $unicodeIdentityMap = $this->buildToUnicode();
        return new CMap(false);
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

}
