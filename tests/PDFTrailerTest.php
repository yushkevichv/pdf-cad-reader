<?php

namespace Yushkevichv\PDFCadReader\Tests;

use Yushkevichv\PDFCadReader\PDFObjectElement\ElementXRef;
use Yushkevichv\PDFCadReader\PDFTrailer;

class PDFTrailerTest extends BaseTestCase
{
    public function testCreateTrailer()
    {
        $data = [
            'Root' => new ElementXRef('root_link'),
            'Size' => 1,
        ];

        $pdfTrailer = new PDFTrailer($data);
        $this->assertTrue($pdfTrailer instanceof PDFTrailer);
        $this->assertEquals('root_link', $pdfTrailer->getRoot());
    }

    public function testInvalidCreateTrailer()
    {
        $data = [
            'Root' => 'root_link',
        ];

        $this->expectException('\Exception');
        $pdfTrailer = new PDFTrailer($data);
    }
}
