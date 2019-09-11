<?php

namespace Yushkevichv\PDFCadReader;

class PDFCadReader
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function parseFile(string $filename)
    {
        $content = file_get_contents($filename);
        $parser = new Parser();
        $data = $parser->parseContent($content);

        return $data;
    }

}
