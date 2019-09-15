<?php

namespace Yushkevichv\PDFCadReader;

use Exception;
use Yushkevichv\PDFCadReader\PDFFont\FontInfo;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementXRef;
use Yushkevichv\PDFCadReader\PDFFont\PDFFont;


class PDFObject
{
    protected $objects = [];
    protected $index = [];
    protected $streamData = [];
    protected $trailer;

    public function __construct()
    {
        $this->objects = [];
        $this->index = [];
        $this->streamData = [];
        $this->trailer = null;
    }

    /**
     * @return array
     */
    public function getIndex(): array
    {
        return $this->index;
    }

    public function getStreamData() :array
    {
        return $this->streamData;
    }

    private function setStreamData()
    {
        $streamKeys = $this->index['mappers']['streams'];
        foreach ($streamKeys as $key => $stream) {
            $this->streamData[$key] = $this->getObjectById($stream)[1];
        }
    }

    /**
     * @return PDFTrailer
     */
    public function getTrailer(): PDFTrailer
    {
        return $this->trailer;
    }

    /**
     * @return string
     */
    private function getRoot()
    {
        return (string) $this->getTrailer()->getRoot();
    }

    /**
     * @param array $objects
     */
    public function setObjects(array $objects)
    {
        $this->objects = $objects;
    }

    /**
     * @param PDFTrailer $trailer
     */
    public function setTrailer(PDFTrailer $trailer)
    {
        $this->trailer = $trailer;
    }

    /**
     * Build index for file.
     *
     * @throws Exception
     */
    public function buildIndex()
    {
        $rootKey = $this->getRoot();
        $this->index['root'] = $rootKey;

        $root = $this->getObjectById($rootKey)[0];
        $pages = $this->getObjectById((string) $root['Pages'])[0];

        if ($pages['Count'] != 1) {
            throw new Exception('Multipage PDF not supported');
        }

        $kids = $this->getKids((string) $pages['Kids'][0]);

        $this->index['info']['width'] = ($kids['MediaBox'][2] - $kids['MediaBox'][0]) ?? 0;
        $this->index['info']['height'] = ($kids['MediaBox'][3] - $kids['MediaBox'][1]) ?? 0;
        if (isset($kids['Rotate']) && array_key_exists('Rotate', $kids)) {
            $this->index['info']['rotate'] = (int) $kids['Rotate'];
        } else {
            $this->index['info']['rotate'] = 0;
        }

        if (count($kids['Contents']) > 1) {
            $this->index['mappers']['layers'] = $this->getLayersMapper($kids['Resources']['Properties']);
            $this->index['mappers']['fonts'] = $this->getFontMapper($kids['Resources']['Font']);
            $this->index['layers'] = $this->getLayers($root);
        }

        $this->index['mappers']['streams'] = array_values($this->getLayersMapper($kids['Contents']));

        $this->setStreamData();
    }

    private function getKids($key)
    {
        $kids = $this->getObjectById($key)[0];
        if (isset($kids['Kids']) && array_key_exists('Kids', $kids) && isset($kids['Kids'][0])) {
            return $this->getKids((string) $kids['Kids'][0]);
        }

        return $kids;
    }

    /**
     * @param $key
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function getObjectById($key)
    {
        if (!isset($this->objects[$key]) || (!array_key_exists($key, $this->objects))) {
            throw new Exception('Object not found for key '.$key);
        }

        return $this->objects[$key];
    }

    /**
     * @param $layers
     *
     * @return array
     */
    private function getLayersMapper($layers): array
    {
        $mapper = [];
        foreach ($layers as $code => $layer) {
            $mapper[$code] = (string) $layer;
        }

        return $mapper;
    }

    /**
     * @param array $fonts
     *
     * @return array
     */
    private function getFontMapper(array $fonts): array
    {

        $mapper = [];
        foreach ($fonts as $code => $layer) {
            $fontObject = $this->buildFontObject($code, (string) $layer);
            dd($fontObject);
            // @todo implement work with fonts
            $mapper[$code] = [
                'layer'      => (string) $layer,
                'fontFamily' => 'Arial',
                'font' => $fontObject
            ];
        }

        return $mapper;
    }

    private function buildFontObject($fontCode, string $layer) :PDFFont
    {
        $fontObject = new PDFFont();
        $fontObject->code = $fontCode;
        $font = $baseFontInfo = $this->getObjectById($layer)[0];
        $composite = false;

        $fontObject->subType = $font['Subtype'];
        if($fontObject->subType == 'Type0') {
            // If font is a composite
            //  - get the descendant font
            //  - set the type according to the descendant font
            //  - get the FontDescriptor from the descendant font

            if(is_array($font['DescendantFonts'])) {
                $font = $font['DescendantFonts'][0];
            }
            else {
                $font = $font['DescendantFonts'];
            }
            $composite = true;
        }

        $descriptor = $this->getObjectById((string) $font['FontDescriptor'])[0];
        // work with hash?

        $fontObject->encoding = $baseFontInfo['Encoding'];
        $fontObject->name = $descriptor['FontName'];

        $fontObject->flags = $descriptor['Flags'];
        $fontObject->composite = $composite;

        $fontObject->firstChar = $font['FirstChar'] ?? 0;
        $fontObject->lastChar = $font['LastChar'] ?? ($composite ? 0xFFFF : 0xFF);

        $fontInfo = new FontInfo($descriptor);
        $fontObject->fontInfo = $fontInfo;

        $fontFile = $descriptor['FontFile'] ?? $descriptor['FontFile2'] ?? $descriptor['FontFile3'] ?? null;
        if($fontFile && ($fontFile instanceof ElementXRef)) {
            $fontFile = $this->getObjectById((string) $fontFile);
        }
        dd($fontFile);

        dd($descriptor);

        dd($font);


        return $fontObject;
    }

    /**
     * @param array $layers
     *
     * @throws Exception
     *
     * @return array
     */
    private function getLayers(array $root): array
    {
        $ocProperties = $root['OCProperties'];
        if ($ocProperties instanceof ElementXRef) {
            $layers = $this->getObjectById((string) $root['OCProperties'])[0]['D']['Order'];
        } else {
            $layers = $root['OCProperties']['D']['Order'];
        }

        $mapper = [];

        foreach ($layers as $code => $layer) {
            $mapper[(string) $layer] = $this->getObjectById((string) $layer)[0]['Name'];
        }

        return $mapper;
    }
}
