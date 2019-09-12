<?php


namespace Yushkevichv\PDFCadReader;


class PDFObject
{
    protected $objects = [];
    public $trailer;

    public function setObjects(array $objects)
    {
        $this->objects = $objects;
    }

    public function setTrailer(PDFTrailer $trailer)
    {
        $this->trailer = $trailer;

    }

}
