<?php

namespace Application\XmlReader;

use Application\Entity\User;

class Authorization extends XmlReader
{

    public function read($xml, User $user)
    {
        $xmlArray = $this->convert($xml);

        $user->setFio($xmlArray['FIO_Designer']);
        $user->setId($xmlArray['ID_Designer']);
        $user->setRole($xmlArray['UserRule']);
        if(isset($xmlArray['CipherList']['Cipher'])) {
            if(is_string($xmlArray['CipherList']['Cipher'])) {
                $xmlArray['CipherList']['Cipher'] = array($xmlArray['CipherList']['Cipher']);
            }
            foreach($xmlArray['CipherList']['Cipher'] as $cipher) {
                $user->addCipher($cipher);
            }
        }

        return $user;
    }

}