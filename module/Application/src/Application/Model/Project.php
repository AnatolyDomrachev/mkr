<?php
namespace Application\Model;

class Project
{

public function getModelUser()
    {
        return $this->getServiceLocator()->get('Model\User');
    }

public function commonNodesAction2()
	{
		$user = $this->getModelUser()->getFromSession();
        $requestConfig = new RequestConfig();
        $query = $this->getRequest()->getQuery();
        $pes = array();

        if(isset($query->cipher) && !empty($query->cipher) && isset($query->peNumber) && !empty($query->peNumber)) {
            $cipher = $this->getRequest()->getQuery()->cipher;
            $generator  = new \Application\Generator\Xml();
            $project    = $generator->generateProjectViewForGip($user, $cipher);
            $request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
            $request->setBody($project);
            $response = $request->request();
            if($response->isSuccess()) {
                $reader  = new \Application\XmlReader\Project();
                $project = $reader->read($response->getXml());

                $pe = $project->getPe($query->peNumber);
                $peNumber = $pe->getNumber();

                $prevPeNumber = $pe->getNumber() - 1;
                if($project->isExistPe($prevPeNumber)) {
                    $prevPe = $project->getPe($prevPeNumber);
                    foreach($prevPe->getCombinations() as $id => $values) {
                        $s = $prevPe->getSCombination($id);
                        if(!empty($s) && $id == $pe->getNumber()) {
                            for($i = 1; $s >= $i; $i++) {
                                $pes[] = array(
                                    'pe_number' => $prevPeNumber,
                                    'node_number' => $i,
                                    'section_number' => $prevPeNumber < $peNumber ? $prevPeNumber : $peNumber,
                                );
                            }
                            break;
                        }
                    }
                }

                foreach($pe->getCombinations() as $id => $values) {
                    $s = $pe->getSCombination($id);
                    for($i = 1; $s >= $i; $i++) {
                        $pes[] = array(
                            'pe_number' => $id,
                            'node_number' => $i,
                            'section_number' => $id < $peNumber ? $id : $peNumber,
                        );
                    }
                }
            }
        } else {
            throw new \ErrorException('Не передан шифр проекта');
        }

       return array(
            'cipher' => $project->getCipher(),
            'bps' => $bps,
            'pes' => $pes,
            'commonPe' => $pe,
            'currentPe' => $project->getPe($query->peNumber),
			);

	}
}
	?>