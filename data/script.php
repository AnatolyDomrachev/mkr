<?php

use \Application\Request;

/**
 * @var $functionNumber
 * @var $args
 */
require_once 'ScriptTool.php';

$statuses = new \Application\Db\Table\ResponseStatus();
$success = $statuses->getByAlias('success');
$error = $statuses->getByAlias('error');
$errorAuthorization = $statuses->getByAlias('error_authorization');

switch($functionNumber) {
    case ScriptTool::getFunctionNumber(Request::TYPE_AUTHORIZATION):
        $uuid = array_shift($args);
        try {
            $reader = new XMLReader();
            $reader->open(ScriptTool::getRequestFile(Request::TYPE_AUTHORIZATION, $uuid));
            $login = null;
            $password = null;
            while ($reader->read()) {
                switch ($reader->name) {
                    case "Login":
                        $reader->read();
                        $login = $reader->value;
                        $reader->read();
                        break;
                    case "Password":
                        $reader->read();
                        $password = $reader->value;
                        $reader->read();
                        break;
                }
            }
            $reader->close();

            if(!is_null($login) && !is_null($password)) {
                $needle_user = null;
                foreach(ScriptTool::getUsers() as $user) {
                    if($user['login'] === $login && md5($user['password']) === $password) {
                        $needle_user = $user;
                    }
                }

                if(!is_null($needle_user)) {
                    $responseFile = ScriptTool::getResponseFile(Request::TYPE_AUTHORIZATION, $uuid);
                    return file_put_contents($responseFile, ScriptTool::get_authorization_xml($needle_user)) !== false
                        ? $success
                        : $error;
                }
            }
            return $errorAuthorization;
        } catch (Exception $e) {
            return $error;
        }

    case ScriptTool::getFunctionNumber(Request::TYPE_USERS):
        $uuid = array_shift($args);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
        $xml .= '<Users>';
        foreach(ScriptTool::getUsers() as $user) {
            $xml .= "<User ID=\"{$user['id']}\">";
            $xml .= "<FIO>{$user['fio']}</FIO>";
            $xml .= "<IP_Designer>{$user['ip']}</IP_Designer>";
            $xml .= "<Description>{$user['description']}</Description>";
            $xml .= "<MAC>{$user['mac']}</MAC>";
            $xml .= "<Password>{$user['password']}</Password>";
            $xml .= "<UserRule>{$user['role']}</UserRule>";
            $xml .= "<Login>{$user['login']}</Login>";
            $xml .= "</User>";
        }
        $xml .= '</Users>';

        return (file_put_contents(ScriptTool::getResponseFile(Request::TYPE_USERS, $uuid), $xml) !== false)
            ? $success
            : $error;

    case ScriptTool::getFunctionNumber(Request::TYPE_PROJECT_CREATE):
        $uuid = array_shift($args);
        $xml = file_get_contents(ScriptTool::getRequestFile(Request::TYPE_PROJECT_CREATE, $uuid));
        foreach(ScriptTool::getElements($xml) as $element) {
            if($element['tag'] == 'CIPHER' && $element['type'] == 'complete') {
                if(!array_key_exists('tag', $element) || empty($element['tag'])) {
                    return $error;
                }
                $projectCipher = md5($element['value']);
                $result = file_put_contents(dirname(__FILE__) . '/usersData/fake/projects/'. $projectCipher, $xml);

                return ($result !== false)
                    ? $success
                    : $error;
            }
        }
        return $error;

    case ScriptTool::getFunctionNumber('projectViewForGip'):
        $uuid = array_shift($args);
        $xml = file_get_contents(ScriptTool::getRequestFile('projectViewForGip', $uuid));
        $xmlArray = ScriptTool::convertXml($xml);
        $cipherNeeded = $xmlArray['Cipher'];

        foreach(ScriptTool::getProjects() as $cipher => $projectXml) {
            if($cipherNeeded == $cipher) {
                return file_put_contents(ScriptTool::getResponseFile('projectViewForGip', $uuid), $projectXml)
                    ? $success
                    : $error;
            }
        }
        return $error;

    case ScriptTool::getFunctionNumber('projectViewForDesigner'):
        $uuid = array_shift($args);
        $xml = file_get_contents(ScriptTool::getRequestFile('projectViewForDesigner', $uuid));
        $xmlArray = ScriptTool::convertXml($xml);
        $cipherNeeded = $xmlArray['Cipher'];

        foreach(ScriptTool::getProjects() as $cipher => $projectXml) {
            if($cipherNeeded == $cipher) {
                return file_put_contents(ScriptTool::getResponseFile('projectViewForDesigner', $uuid), $projectXml)
                    ? $success
                    : $error;
            }
        }
        return $error;

    case ScriptTool::getFunctionNumber('projectWorkForDesigner'):
        $uuid = array_shift($args);
        $xml = file_get_contents(ScriptTool::getRequestFile('projectWorkForDesigner', $uuid));
        $xmlArray = ScriptTool::convertXml($xml);
        $cipherNeeded = $xmlArray['Cipher'];

        foreach(ScriptTool::getProjects() as $cipher => $projectXml) {
            if($cipherNeeded == $cipher) {
                return file_put_contents(ScriptTool::getResponseFile('projectWorkForDesigner', $uuid), $projectXml)
                    ? $success
                    : $error;
            }
        }
        return $error;

    case ScriptTool::getFunctionNumber('projectCommonNode'):
        return $success;

    case ScriptTool::getFunctionNumber('hashString'):
        $string = array_shift($args);
        return md5($string);

    case ScriptTool::getFunctionNumber('peStage'):
        $stages = ScriptTool::getPeStages();
        $cipher = array_shift($args);
        $peNumber = array_shift($args);

        if(!$cipher || !$peNumber) {
            throw new ErrorException("Не все параметры переданы cipher: ($cipher), peNumber: ($peNumber)");
        }

        $rand = $stages[array_rand($stages)];
        return $rand['number'];

    default:
        throw new ErrorException("Не известный метод ($functionNumber)");
}