<?php


namespace Yushkevichv\PDFCadReader\PDFFont;


class FontFileData
{
    public $fontObject;

    const VALID_TABLES = [
        'OS/2', 'cmap', 'head', 'hhea', 'hmtx', 'maxp',
        'name', 'post', 'loca', 'glyf', 'fpgm', 'prep', 'cvt ', 'CFF '
    ];

    public $TTOpsStackDeltas = [
        0, 0, 0, 0, 0, 0, 0, 0, -2, -2, -2, -2, 0, 0, -2, -5,
        -1, -1, -1, -1, -1, -1, -1, -1, 0, 0, -1, 0, -1, -1, -1, -1,
        1, -1, -999, 0, 1, 0, -1, -2, 0, -1, -2, -1, -1, 0, -1, -1,
        0, 0, -999, -999, -1, -1, -1, -1, -2, -999, -2, -2, -999, 0, -2, -2,
        0, 0, -2, 0, -2, 0, 0, 0, -2, -1, -1, 1, 1, 0, 0, -1,
        -1, -1, -1, -1, -1, -1, 0, 0, -1, 0, -1, -1, 0, -999, -1, -1,
        -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
        -2, -999, -999, -999, -999, -999, -1, -1, -2, -2, 0, 0, 0, 0, -1, -1,
        -999, -2, -2, 0, 0, -1, -2, -2, 0, 0, 0, -1, -1, -1, -2];
        // 0xC0-DF == -1 and 0xE0-FF == -2

    public function __construct(PDFFont $fontObject)
    {
        $this->fontObject = $fontObject;
        $this->checkAndRepair();
    }

    private function checkAndRepair()
    {
        $header = $tables = null;

        $byteArray = unpack('C*', $this->fontObject->fontFile->stream);
        $header = $this->readOpenTypeHeader($byteArray);

        dd($byteArray);

    }

    private function readOpenTypeHeader($fontStream)
    {
        dd(1);
//        $version = bytesToString($fontStream.getBytes(4));
        return [
//            "version" => bytesToString(ttf.getBytes(4)),
//            "numTables" => ttf.getUint16(),
//            "searchRange" => ttf.getUint16(),
//            "entrySelector" => ttf.getUint16(),
//            "rangeShift" => ttf.getUint16(),
        ];

    }

}
