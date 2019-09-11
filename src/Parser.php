<?php


namespace Yushkevichv\PDFCadReader;


use Yushkevichv\PDFCadReader\PDFObjectElement\ElementArray;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementHexa;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementName;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementString;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementXRef;

class Parser
{
    protected $objects = [];

    public function parseContent($content)
    {
        // Create structure using TCPDF Parser.
        ob_start();
        @$parser = new \TCPDF_PARSER(ltrim($content));
        list($xref, $data) = $parser->getParsedData();
        unset($parser);
        ob_end_clean();

        if (isset($xref['trailer']['encrypt'])) {
            throw new \Exception('Secured pdf file are currently not supported.');
        }
        if (empty($data)) {
            throw new \Exception('Object list not found. Possible secured file.');
        }

//        // Create destination object.
        $object      = new PDFObject();
        $this->objects = [];
//
        foreach ($data as $id => $structure) {
            $object = $this->parseObject($id, $structure, $object);
            array_push($this->objects, $object);
            unset($data[$id]);
        }
        dd($this->objects);

        dd($xref);

//
//        $document->setTrailer($this->parseTrailer($xref['trailer'], $document));
//        $document->setObjects($this->objects);
//        return $document;

    }

    public function parse($data)
    {
        if(empty($data)) {
            throw new \Exception('Data is empty');
        }

        // find the pdf header starting position
        if (($trimpos = strpos($data, '%PDF-')) === false) {
            throw new \Exception('Invalid PDF data: missing %PDF header.');
        }

    }

    /**
     * @param string   $id
     * @param array    $structure
     * @param PDFObject $object
     */
    protected function parseObject($id, $structure)
    {

        $content = '';
        $result = [];

        foreach ($structure as $position => $part) {
            switch ($part[0]) {
                case '<<' :
                    $result[] = [
                        'id' => $id,
                        'data' => $this->parseStructure($part[1])
                    ];
                break;
                case 'stream':
                    $content = isset($part[3][0]) ? $part[3][0] : $part[1];
                    dd($content);
//                    if ($header->get('Type')->equals('ObjStm')) {
//                        $match = array();
//                        // Split xrefs and contents.
//                        preg_match('/^((\d+\s+\d+\s*)*)(.*)$/s', $content, $match);
//                        $content = $match[3];
//                        // Extract xrefs.
//                        $xrefs = preg_split(
//                            '/(\d+\s+\d+\s*)/s',
//                            $match[1],
//                            -1,
//                            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
//                        );
//                        $table = array();
//                        foreach ($xrefs as $xre   f) {
//                            list($id, $position) = explode(' ', trim($xref));
//                            $table[$position] = $id;
//                        }
//                        ksort($table);
//                        $ids       = array_values($table);
//                        $positions = array_keys($table);
//                        foreach ($positions as $index => $position) {
//                            $id            = $ids[$index] . '_0';
//                            $next_position = isset($positions[$index + 1]) ? $positions[$index + 1] : strlen($content);
//                            $sub_content   = substr($content, $position, $next_position - $position);
//                            $sub_header         = Header::parse($sub_content, $document);
//                            $object             = PDFObject::factory($document, $sub_header, '');
//                            $this->objects[$id] = $object;
//                        }
//                        // It is not necessary to store this content.
//                        $content = '';
//                        return;
//                    }
                    break;
                default:
//                    dd($result);
                    dd($part[0]);
//                    dd('default');
                break;
            }
        }

        return $result;
//        $header  = new Header(array(), $document);
//        $content = '';
//        foreach ($structure as $position => $part) {
//            switch ($part[0]) {
//                case '[':
//                    $elements = array();
//                    foreach ($part[1] as $sub_element) {
//                        $sub_type   = $sub_element[0];
//                        $sub_value  = $sub_element[1];
//                        $elements[] = $this->parseHeaderElement($sub_type, $sub_value, $document);
//                    }
//                    $header = new Header($elements, $document);
//                    break;
//                case '<<':
//                    $header = $this->parseHeader($part[1], $document);
//                    break;
//                case 'stream':
//                    $content = isset($part[3][0]) ? $part[3][0] : $part[1];
//                    if ($header->get('Type')->equals('ObjStm')) {
//                        $match = array();
//                        // Split xrefs and contents.
//                        preg_match('/^((\d+\s+\d+\s*)*)(.*)$/s', $content, $match);
//                        $content = $match[3];
//                        // Extract xrefs.
//                        $xrefs = preg_split(
//                            '/(\d+\s+\d+\s*)/s',
//                            $match[1],
//                            -1,
//                            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
//                        );
//                        $table = array();
//                        foreach ($xrefs as $xref) {
//                            list($id, $position) = explode(' ', trim($xref));
//                            $table[$position] = $id;
//                        }
//                        ksort($table);
//                        $ids       = array_values($table);
//                        $positions = array_keys($table);
//                        foreach ($positions as $index => $position) {
//                            $id            = $ids[$index] . '_0';
//                            $next_position = isset($positions[$index + 1]) ? $positions[$index + 1] : strlen($content);
//                            $sub_content   = substr($content, $position, $next_position - $position);
//                            $sub_header         = Header::parse($sub_content, $document);
//                            $object             = PDFObject::factory($document, $sub_header, '');
//                            $this->objects[$id] = $object;
//                        }
//                        // It is not necessary to store this content.
//                        $content = '';
//                        return;
//                    }
//                    break;
//                default:
//                    if ($part != 'null') {
//                        $element = $this->parseHeaderElement($part[0], $part[1], $document);
//                        if ($element) {
//                            $header = new Header(array($element), $document);
//                        }
//                    }
//                    break;
//            }
//        }
//        if (!isset($this->objects[$id])) {
//            $this->objects[$id] = PDFObject::factory($document, $header, $content);
//        }
    }


    protected function parseStructure($structure)
    {
        $elements = [];
        $count    = count($structure);

        for ($position = 0; $position < $count; $position += 2) {
            $name  = $structure[$position][1];
            $type  = $structure[$position + 1][0];
            $value = $structure[$position + 1][1];
            $elements[$name] = $this->parseStructureElement($type, $value);
//            dump($elements);

        }
        return $elements;
//        return new Header($elements, $document);
    }

    protected function parseStructureElement($type, $value)
    {

        switch ($type) {
            case '<<':
                return $this->parseStructure($value);
            case 'numeric':
//                return new ElementNumeric($value, $document);
//            case 'boolean':
//                return new ElementBoolean($value, $document);
//            case 'null':
//                return new ElementNull($value, $document);
            case '(':
//                if ($date = ElementDate::parse('(' . $value . ')', $document)) {
//                    return $date;
//                } else {
                    return ElementString::parse('(' . $value . ')');
//                }
            case '<':
                return $this->parseStructureElement('(', Encoder::decodeHexa($value));
            case '/':
                return ElementName::parse('/' . $value);
            case 'ojbref': // old mistake in tcpdf parser
            case 'objref':
                return new ElementXRef($value);
            case '[':
                $values = [];
                foreach ($value as $sub_element) {
                    $sub_type  = $sub_element[0];
                    $sub_value = $sub_element[1];
                    $values[]  = $this->parseStructureElement($sub_type, $sub_value);
                }
                return new ElementArray($values);
//            case 'endstream':
//            case 'obj': //I don't know what it means but got my project fixed.
//            case '':
//                // Nothing to do with.
//                break;
            default:
                dd($type, $value);
                throw new \Exception('Invalid type: "' . $type . '".');
        }
    }
}
