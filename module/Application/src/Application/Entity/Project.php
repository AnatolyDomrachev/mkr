<?php

namespace Application\Entity;

class Project {

    protected $name;
    protected $gipId;
    protected $cipher;
    protected $creationTime;
    protected $numberProjElements;
    protected $listPe = array();

    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
    }

    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * @return Pe
     */
    public function getUserPe(User $user)
    {
        foreach($this->getListPe() as $pe) {
            if($pe->getDesignerId() == $user->getId()) {
                return $pe;
            }
        }
        throw new \ErrorException("Не найдено PE для текущего пользователя ($userId)");
    }

    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
    }

    public function getCreationTime()
    {
        return $this->creationTime;
    }

    public function setGipId($gipId)
    {
        $this->gipId = $gipId;
    }

    public function getGipId()
    {
        return $this->gipId;
    }

    public function addPe(Pe $pe)
    {
        $this->listPe[$pe->getNumber()] = $pe;
    }

    /**
     * @return Pe
     */
    public function getPe($number)
    {
        if(!$this->isExistPe($number)) throw new \ErrorException('Не известный номер PE');

        return $this->listPe[$number];
    }

    public function isExistPe($number)
    {
        return array_key_exists($number, $this->listPe);
    }

    /** @return Pe[] */
    public function getListPe()
    {
        return $this->listPe;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setNumberProjElements($numberProjElements)
    {
        $this->numberProjElements = $numberProjElements;
    }

    public function getNumberProjElements()
    {
        return $this->numberProjElements;
    }

}