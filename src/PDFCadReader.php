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
     * @param  string  $filename
     *
     * @return PDFObject
     * @throws \Exception
     */
    public function parseFile(string $filename) : PDFObject
    {
        $content = file_get_contents($filename);
        $parser = new Parser();

        return $parser->parseContent($content);
    }

}
