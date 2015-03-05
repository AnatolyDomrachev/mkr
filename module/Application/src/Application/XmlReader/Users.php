<?php

namespace Application\XmlReader;

class Users extends XmlReader
{
    public function read($xml)
    {
        $xmlArray = $this->convert($xml);
        $users = array();
        foreach($xmlArray['User'] as $user) {
            $users[] = array(
                'id' => $user['@attributes']['ID'],
                'fio' => $user['FIO'],
                'ip' => $user['IP_Designer'],
                'mac' => $user['MAC'],
                'role' => $user['Role'],
                'login' => $user['Login'],
                'password' => $user['Password'],
                'description' => $user['Description'],
            );
        }

        return $users;
    }
}