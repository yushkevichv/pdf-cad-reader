<?php


namespace Yushkevichv\PDFCadReader\PDFFont;



class CMap
{
    public $codespaceRanges;
    public $numCodespaceRanges;
    public $_map;
    public $name;
    public $vertical;
    public $useCMap;
    public $builtInCMap;

    public function __construct($builtInCMap = false)
    {
        // Codespace ranges are stored as follows:
        // [[1BytePairs], [2BytePairs], [3BytePairs], [4BytePairs]]
        // where nBytePairs are ranges e.g. [low1, high1, low2, high2, ...]
        $this->codespaceRanges = [[], [], [], []];
        $this->numCodespaceRanges = 0;
        // Map entries have one of two forms.
        // - cid chars are 16-bit unsigned integers, stored as integers.
        // - bf chars are variable-length byte sequences, stored as strings, with
        //   one byte per character.
        $this->_map = [];
        $this->name = '';
        $this->vertical = false;
        $this->useCMap = null;
        $this->builtInCMap = $builtInCMap;

    }

    public static function create(PDFFont $pdfFont)
    {
        $encoding = $pdfFont->encoding;
        if(is_string($encoding)) {
            return self::createBuiltInCMap($encoding);
        }
        else {
            // @todo add support stream encoding
            throw new \Exception('todo Add support stream encoding');
        }

    }

    private static function createBuiltInCMap($encoding)
    {
        if ($encoding === 'Identity-H') {
            return new IdentityCMap(false, 2);
        }

        // @todo implement other variants
        throw new \Exception('todo implement CMAP for other encoding');
    }

}
