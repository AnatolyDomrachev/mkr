<?php

namespace Application\Entity;

class User
{

    const MODE_SINGLE = 'single';
    const MODE_MULTI  = 'multi';

    const ROLE_DESIGNER  = 'Designer';
    const ROLE_GIP  = 'GIP';

    private $id;
    private $login;
    private $password;
    private $mode;
    private $role;
    private $ciphers = array();
    private $fio;
    private $ip;
    private $mac;
    private $description;

    public static function roles()
    {
        return array(
            self::ROLE_DESIGNER,
            self::ROLE_GIP,
        );
    }

    public static function roleTitle($role)
    {
        switch($role) {
            case self::ROLE_DESIGNER:
                return 'Проектировщик';
            case self::ROLE_GIP:
                return 'Руководитель';
            default:
                throw new \ErrorException('Неизвестная роль');
        }
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    public function getFio()
    {
        return $this->fio;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setMac($mac)
    {
        $this->mac = $mac;
    }

    public function getMac()
    {
        return $this->mac;
    }

    public function setCiphers(array $ciphers)
    {
        $this->ciphers = $ciphers;
    }

    public function getCiphers()
    {
        return $this->ciphers;
    }

    public function addCipher($cipher)
    {
        $this->ciphers[] = $cipher;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function isSingleMode()
    {
        return $this->getMode() == self::MODE_SINGLE;
    }

    public function isMultiMode()
    {
        return $this->getMode() == self::MODE_MULTI;
    }

    public function isDesignerRole()
    {
        return $this->getRole() == self::ROLE_DESIGNER;
    }

    public function isGipRole()
    {
        return $this->getRole() == self::ROLE_GIP;
    }

}