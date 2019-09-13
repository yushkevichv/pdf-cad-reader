<?php

namespace Yushkevichv\PDFCadReader\Tests;


use Yushkevichv\PDFCadReader\Parser;
use Mockery as m;

class ParserTest extends BaseTestCase
{

    public function testParseEncryptPdf()
    {
        $xref['trailer']['encrypt'] = true;
        $data = [];
        $mockParser =  m::mock(\TCPDF_PARSER::class);
        $parser = new Parser();
        $mockParser->shouldReceive('getParsedData')
            ->once()->andReturn([$xref, $data]);

        $this->expectException('\Exception');

        $parser->parseContent($mockParser);
    }

    public function testParseEmptyData()
    {
        $mockParser =  m::mock(\TCPDF_PARSER::class);
        $parser = new Parser();
        $mockParser->shouldReceive('getParsedData')
            ->once()->andReturn([$xref = [], $data = []]);

        $this->expectException('\Exception');

        $parser->parseContent($mockParser);
    }
}
