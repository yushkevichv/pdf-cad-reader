<?php

namespace Yushkevichv\PDFCadReader;

class PDFCadReader
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * @param string $filename
     *
     * @throws \Exception
     *
     * @return PDFObject
     */
    public function parseFile(string $filename) : PDFObject
    {
        // @todo addd check for invalid data
        $content = file_get_contents($filename);
        $parser = new Parser();

        return $parser->parseContent(new \TCPDF_PARSER(ltrim($content)));
    }
}
