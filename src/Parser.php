<?php


namespace Yushkevichv\PDFCadReader;


use Yushkevichv\PDFCadReader\PDFObjectElement\ElementArray;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementName;
use Yushkevichv\PDFCadReader\PDFObjectElement\ElementNumeric;
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
        $pdfObject      = new PDFObject();
        $this->objects = [];
//
        foreach ($data as $id => $structure) {
            $object = $this->parseObject($id, $structure);
            if($object) {
                $this->objects[$id] = $object;
            }
            unset($data[$id]);
        }
        $pdfObject->setObjects($this->objects);
        $pdfObject->setTrailer($this->parseTrailer($xref['trailer']));

        return $pdfObject;
    }

    /**
     * @param string   $id
     * @param array    $structure
     */
    protected function parseObject($id, $structure)
    {
        foreach ($structure as $position => $part) {
            switch ($part[0]) {
                case '<<' :
                    $result[$position] = $this->parseStructure($part[1]);
                break;
                case 'stream':
                    $result[$position] = isset($part[3][0]) ? $part[3][0] : $part[1];
                    break;
                case '[':
                    $elements = array();
                    foreach ($part[1] as $sub_element) {
                        $sub_type   = $sub_element[0];
                        $sub_value  = $sub_element[1];
                        $element = $this->parseStructureElement($sub_type, $sub_value);
                        if($element) {
                            $elements[] = $element;
                        }
                    }
                    if($elements) {
                        $result[$position] = $elements;
                    }
                break;
                default:
                    if ($part != 'null') {
                        $element = $this->parseStructureElement($part[0], $part[1]);
                        if ($element) {
                            // @todo catcher
                        }
                    }
                    break;
                break;
            }

        }

        if($result) {
            return $result;
        }

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
        }
        return $elements;
    }

    protected function parseStructureElement($type, $value)
    {
        switch ($type) {
            case '<<':
                return $this->parseStructure($value);
            case 'numeric':
                return ElementNumeric::parse($value);
            break;
            case '(':
                    return ElementString::parse('(' . $value . ')');
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
                return $values;
            break;
            case 'endstream':

            break;
//            case 'obj': //I don't know what it means but got my project fixed.
//            case '':
//                // Nothing to do with.
//                break;
            default:
                dd($type, $value);
                throw new \Exception('Invalid type: "' . $type . '".');
        }
    }

    protected function parseTrailer($structure)
    {
        $trailer = array();
        foreach ($structure as $name => $values) {
            $name = ucfirst($name);
            if (is_numeric($values)) {
                $trailer[$name] = $values;
            } elseif (is_array($values)) {
                $value          = $this->parseTrailer($values, null);
                $trailer[$name] = new ElementArray($value, null);
            } elseif (strpos($values, '_') !== false) {
                $trailer[$name] = new ElementXRef($values);
            } else {
                $trailer[$name] = $this->parseHeaderElement('(', $values);
            }
        }

        return new PDFTrailer($trailer);
    }

}
