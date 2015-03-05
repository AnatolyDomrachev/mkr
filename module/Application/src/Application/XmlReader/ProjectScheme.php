<?php

namespace Application\XmlReader;

use Application\Entity\Pe;
use Application\Entity\User;

class Project extends XmlReader
{

    public function read($xml)
    {
        $xmlArray = $this->convert($xml);
/*         $project  = new \Application\Entity\Project();

        $project->setName($this->string($xmlArray['ObjectName']));
        $project->setGipId($this->string($xmlArray['ID_GIP']));
        $project->setCipher($this->string($xmlArray['Cipher']));
        $project->setCreationTime($this->string($xmlArray['CreationTime']));
        $project->setNumberProjElements($this->string($xmlArray['NumberProjElements']));

        foreach($xmlArray['ListPE']['ID'] as $peArray) {
            $pe = new Pe();
            $pe->setNumber($peArray['@attributes']['PE']);
            $pe->setDesignerId($peArray['ID_Designer']);
            $pe->setKx($this->string($peArray['KX']));
            $pe->setKy($this->string($peArray['KY']));
            $pe->setKz($this->string($peArray['KZ']));
            $pe->setDx($this->string($peArray['DX']));
            $pe->setDy($this->string($peArray['DY']));
            $pe->setDz($this->string($peArray['DZ']));
            $pe->setFile($this->string($peArray['File']));
            $pe->setDescription($this->string($peArray['Description']));

            $project->addPe($pe);
        }

        if(isset($xmlArray['CombinationPE']['ID'])) {
            if(array_key_exists('@attributes', $xmlArray['CombinationPE']['ID'])) {
                $xmlArray['CombinationPE']['ID'] = array($xmlArray['CombinationPE']['ID']);
            }

            foreach($xmlArray['CombinationPE']['ID'] as $combinationArray) {
                $pe = $project->getPe($combinationArray['@attributes']['PE']);

                if(array_key_exists('@attributes', $combinationArray['PE'])) {
                    $combinationArray['PE'] = array($combinationArray['PE']);
                }

                foreach($combinationArray['PE'] as $peCombination) {
                    $combination = $peCombination['@attributes']['PE'];
                    $combination = explode('-', $combination);
                    $linkPe = $combination[1];

                    $pe->addCombination($linkPe, $peCombination['S'], $peCombination['P'], $peCombination['O']);
                }
            }
        } */

        return $xmlArray;
    }

}