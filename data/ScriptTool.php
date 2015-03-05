<?php

class ScriptTool
{

    static public function get_authorization_xml($user)
    {
        $ciphers = array_keys(self::getProjects());

        $cipherList = '';
        foreach($ciphers as $cipher) {
            if(!empty($cipher)) {
                $cipherList .= "<Cipher>$cipher</Cipher>";
            }
        }

        return <<<EOI
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<Rule>
  <FIO_Designer>{$user['login']}</FIO_Designer>
  <ID_Designer>{$user['id']}</ID_Designer>
  <UserRule>{$user['role']}</UserRule>
  <CipherList>
    {$cipherList}
  </CipherList>
</Rule>
EOI;

    }

    static public function getProjects()
    {
        $projectDir = dirname(__FILE__) . '/usersData/fake/projects/';

        $projects = array();
        foreach (new DirectoryIterator($projectDir) as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->getFilename() == 'empty.txt') continue;

            $projectXml = file_get_contents($projectDir . $fileInfo->getFilename());
            foreach(ScriptTool::getElements($projectXml) as $element) {
                if($element['tag'] == 'CIPHER' && $element['type'] == 'complete') {
                    $projects[$element['value']] = $projectXml;
                    break;
                }
            }
        }

        return $projects;
    }

    static public function getElements($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $elements);
        xml_parser_free($p);

        return $elements;
    }

    static public function getUsers()
    {
        $db = new \PDO('sqlite:'.dirname(__FILE__).'/db/mkr.db');
        return $db->query('SELECT * from `users`')->fetchAll();
    }

    static private function getConfig()
    {
        $config = require dirname(dirname(__FILE__)).'/config/autoload/global.php';
        return (object) $config['application'];
    }

    static public function getFunctionNumber($function)
    {
        $requestConfig = new \Application\Db\Table\RequestConfig();
        return $requestConfig->getNumberByAlias($function);
    }

    static public function getPeStages()
    {
        $config = require dirname(dirname(__FILE__)).'/config/autoload/global.php';
        return $config['application']['stages'];
    }

    static public function getResponseFile($function, $uuid)
    {
        $requestConfig = new \Application\Db\Table\RequestConfig();
        $config = $requestConfig->getByAlias($function);
        $fileName = $config['response_file'];

        return self::getConfig()->response_path.'/'.self::generateFileName($fileName, $uuid);
    }

    static public function getRequestFile($function, $uuid)
    {
        $requestConfig = new \Application\Db\Table\RequestConfig();
        $config = $requestConfig->getByAlias($function);
        $fileName = $config['request_file'];

        return self::getConfig()->request_path.'/'.self::generateFileName($fileName, $uuid);
    }

    static private function generateFileName($name, $uuid)
    {
        return sprintf(self::getConfig()->build_file_name, $name, $uuid);
    }

    static public function convertXml($xml)
    {
        return unserialize(serialize(json_decode(json_encode((array) simplexml_load_string($xml)), 1)));
    }
}