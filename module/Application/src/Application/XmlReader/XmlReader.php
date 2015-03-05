<?php

namespace Application\XmlReader;

class XmlReader
{

    protected function convert($xml)
    {
        return unserialize(serialize(json_decode(json_encode((array) simplexml_load_string($xml)), 1)));
    }

    protected function string($element)
    {
        return empty($element) ? null : $element;
    }

}