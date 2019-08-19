<?php

namespace common\components;

use DOMDocument;

/**
 * Component for converting array to XML (and vice versa)
 * @author Ilya Krylov (kiaplayer@gmail.com)
 */
class XmlPacker
{
    /**
     * Convert array to XML
     * @param array $data
     * @return string
     */
    public function arrayToXml($data)
    {
        $xmlDoc = new DOMDocument('1.0', 'utf-8');
        $xmlRoot = $xmlDoc->createElement('root');
        $xmlDoc->appendChild($xmlRoot);
        $this->_arrayToNode($data, $xmlRoot);
        return $xmlDoc->saveXML();
    }

    /**
     * Convert XML to array
     * @param string $xml
     * @return array
     */
    public function xmlToArray($xml)
    {
        $xmlDoc = new DOMDocument('1.0', 'utf-8');
        $xmlDoc->loadXML($xml);
        return $this->_nodeToArray($xmlDoc->documentElement);
    }

    /**
     * Check array is indexed
     * @param array $arr
     * @return boolean
     */
    private function _isIndexedArray(&$arr)
    {
        return array_keys($arr) === range(0, count($arr) - 1);
    }

    /**
     * Convert array to XML node
     * @param array $data
     * @param \DOMElement $node
     */
    private function _arrayToNode(&$data, $node)
    {
        $isIndexedArray = $this->_isIndexedArray($data);
        if ($isIndexedArray) {
            $node->setAttribute('list', 'true');
        }
        foreach ($data as $key => $value) {
            $subnode = $node->ownerDocument->createElement($isIndexedArray ? 'item' : trim($key));
            $node->appendChild($subnode);
            if (is_array($value)) {
                $this->_arrayToNode($value, $subnode);
            } else {
                $subnode->appendChild($node->ownerDocument->createCDATASection($value));
            }
        }
    }

    /**
     * Convert XML node to array
     * @param \DOMElement $node
     * @return array
     */
    private function _nodeToArray($node)
    {
        $result = '';
        $attributes = array();
        foreach ($node->attributes as $attribute) {
            $attributes[$attribute->nodeName] = $attribute->nodeValue;
        }
        $isIndexedArray = isset($attributes['list']) && $attributes['list'] == 'true';
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1 && in_array($node->firstChild->nodeType, array(XML_TEXT_NODE, XML_CDATA_SECTION_NODE))) {
                $result = $node->firstChild->nodeValue;
                if ($result === 'true') {
                    $result = true;
                } else if ($result === 'false') {
                    $result = false;
                } else if ($result === 'null') {
                    $result = null;
                }
            } else {
                $result = [];
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeName == '#text') {
                        continue;
                    }
                    if ($isIndexedArray) {
                        $result[] = $this->_nodeToArray($childNode);
                    } else {
                        $result[$childNode->nodeName] = $this->_nodeToArray($childNode);
                    }
                }
            }
        } else if ($isIndexedArray) {
            $result = [];
        }
        return $result;
    }
}