<?php

namespace Yushkevichv\PDFCadReader\Tests;

use Yushkevichv\PDFCadReader\PDFObject;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementXRef;
use Yushkevichv\PDFCadReader\PDFTrailer;
use Mockery as m;

class PDFObjectTest extends BaseTestCase
{
    public function testGetObjectById()
    {
        $pdfObject = new PDFObject();
        $objects = ['1_0' => 'data'];
        $pdfObject->setObjects($objects);

        $object = $pdfObject->getObjectById('1_0');
        $this->assertEquals($objects['1_0'], $object);

        $this->expectException('\Exception');
        $pdfObject->getObjectById('error_code');

    }

    public function testBuildMultipagePDFIndex()
    {
        $data = [
            'Root' => new ElementXRef('root_link'),
            'Size' => 1,
        ];

        $pdfTrailer = new PDFTrailer($data);

        $pdfObject =  m::mock(PDFObject::class)->makePartial();
        $pdfObject->setTrailer($pdfTrailer);

        $pdfObject->shouldReceive('getObjectById')
            ->once()->andReturn([0 => ['Pages' => 'data']]);

        $pdfObject->shouldReceive('getObjectById')
            ->once()->andReturn([0 => ['Count' => 2]]);

        $this->expectException('\Exception');

        $pdfObject->buildIndex();
    }


}
