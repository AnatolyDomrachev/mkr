<?php

namespace Application\Entity;

class Pe
{

    private $number;
    private $designerId;

    private $kx;
    private $ky;
    private $kz;

    private $dx;
    private $dy;
    private $dz;

    private $file;
    private $description;

    private $combination = array();

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setCombination($combination)
    {
        $this->combination = $combination;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDesignerId($designerId)
    {
        $this->designerId = $designerId;
    }

    public function getDesignerId()
    {
        return $this->designerId;
    }

    public function setDx($dx)
    {
        $this->dx = $dx;
    }

    public function getDx()
    {
        return $this->dx;
    }

    public function setDy($dy)
    {
        $this->dy = $dy;
    }

    public function getDy()
    {
        return $this->dy;
    }

    public function setDz($dz)
    {
        $this->dz = $dz;
    }

    public function getDz()
    {
        return $this->dz;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getModelFileName()
    {
        return pathinfo($this->file, PATHINFO_BASENAME);
    }

    public function setKx($kx)
    {
        $this->kx = $kx;
    }

    public function getKx()
    {
        return $this->kx;
    }

    public function setKy($ky)
    {
        $this->ky = $ky;
    }

    public function getKy()
    {
        return $this->ky;
    }

    public function setKz($kz)
    {
        $this->kz = $kz;
    }

    public function getKz()
    {
        return $this->kz;
    }

    public function addCombination($linkPe, $s, $p, $o)
    {
        $this->combination[$linkPe] = compact('s', 'p', 'o');
    }

    public function getCombinations()
    {
        return $this->combination;
    }

    public function getSCombination($linkPe)
    {
        return array_key_exists($linkPe, $this->combination) ? $this->combination[$linkPe]['s'] : null;
    }

    public function getPCombination($linkPe)
    {
        return array_key_exists($linkPe, $this->combination) ? $this->combination[$linkPe]['p'] : null;
    }

    public function getOCombination($linkPe)
    {
        return array_key_exists($linkPe, $this->combination) ? $this->combination[$linkPe]['o'] : null;
    }
}