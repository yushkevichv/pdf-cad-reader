<?php


namespace Yushkevichv\PDFCadReader;


class PDFObject
{
    protected $objects = [];
    protected $index = [];
    protected $trailer;

    public function setObjects(array $objects)
    {
        $this->objects = $objects;
    }

    public function setTrailer(PDFTrailer $trailer)
    {
        $this->trailer = $trailer;
    }

    public function buildIndex()
    {
        $rootKey = $this->getRoot();
        $this->index['root'] = $rootKey;

        $root = $this->getObjectById($rootKey)[0];
        $pages = $this->getObjectById((string) $root['Pages'])[0];

        if($pages['Count'] != 1) {
            throw new \Exception('Multipage PDF not supported');
        }

        $kids = $this->getObjectById((string)$pages['Kids'][0])[0];

        $this->index['info']['width'] = ($kids['MediaBox'][2] - $kids['MediaBox'][0]);
        $this->index['info']['height'] = ($kids['MediaBox'][3] - $kids['MediaBox'][1]);
        $this->index['info']['rotate'] = (int) $kids['Rotate'];
        $this->index['mappers']['layers'] = $this->getLayersMapper($kids['Resources']['Properties']);
        $this->index['mappers']['streams'] = $this->getLayersMapper($kids['Contents']);
        $this->index['mappers']['fonts'] = $this->getFontMapper($kids['Resources']['Font']);
        $this->index['layers'] = $this->getLayers($root['OCProperties']['D']['Order']);
        dd($this->index);


        dd($this->trailer);
    }

    public function getLayers(array $layers) :array
    {
        $mapper = [];

        foreach ($layers as $code => $layer)
        {
            $mapper[(string) $layer] = $this->getObjectById((string) $layer)[0]['Name'];
        }

        return $mapper;
    }

    public function getFontMapper(array $fonts) :array
    {
        $fontMapper = [];
        foreach ($fonts as $code => $layer)
        {
            // @todo implement work with fonts
            $mapper[$code] = [
                'layer' => (string) $layer,
                'fontFamily' => 'Arial'
            ];
        }

        return $mapper;
    }

    public function getLayersMapper($layers) : array
    {
        $mapper = [];
        foreach ($layers as $code => $layer)
        {
            $mapper[$code] = (string) $layer;
        }

        return $mapper;
    }

    public function getTrailer() :PDFTrailer
    {
        return $this->trailer;
    }

    public function getRoot()
    {
        return (string) $this->getTrailer()->getRoot();
    }

    public function getObjectById($key)
    {
        if(!isset($this->objects[$key]) || (!array_key_exists($key, $this->objects))) {
            throw new \Exception('Object not found for key '.$key);
        }

        return $this->objects[$key];
    }

}
