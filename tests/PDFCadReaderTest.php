<?php

namespace Yushkevichv\PDFCadReader\Tests;

use Yushkevichv\PDFCadReader\PDFCadReader;
use Yushkevichv\PDFCadReader\PDFObject;

class PDFCadReaderTest extends BaseTestCase
{

    public function testParse()
    {
        $pdfCadReader = new PDFCadReader();
        $data = $pdfCadReader->parseFile($this->dummyPdf);
        $this->assertTrue($data instanceof PDFObject);
    }
}
