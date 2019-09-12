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
        if(
            (isset($trailer['Root']) || array_key_exists('Root', $trailer)) &&
            (isset($trailer['Info']) || array_key_exists('Info', $trailer)) &&
            (isset($trailer['Size']) || array_key_exists('Size', $trailer)) &&
            ($trailer['Root'] instanceof ElementXRef) &&
            ($trailer['Info'] instanceof ElementXRef)

        ) {
            $this->root = $trailer['Root'];
            $this->info = $trailer['Info'];
            $this->size = $trailer['Size'];
        }
        else {
            throw new \Exception('Invalid Trailer');
        }



    }

}
