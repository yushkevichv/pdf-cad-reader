<?php

namespace Yushkevichv\PDFCadReader;

use Yushkevichv\PDFCadReader\PDFObjectElement\ElementXRef;

class PDFTrailer
{
    protected $root;
    protected $info;
    protected $size;

    public function __construct(array $trailer)
    {
        if (
            (isset($trailer['Root']) || array_key_exists('Root', $trailer)) &&
            (isset($trailer['Size']) || array_key_exists('Size', $trailer)) &&
            ($trailer['Root'] instanceof ElementXRef)

        ) {
            $this->root = $trailer['Root'];
            $this->info = $trailer['Info'] ?? null;
            $this->size = $trailer['Size'];
        } else {
            throw new \Exception('Invalid Trailer');
        }
    }

    public function getRoot() :ElementXRef
    {
        return $this->root;
    }
}
