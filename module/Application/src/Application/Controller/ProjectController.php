<?php

namespace Application\Controller;

use Application\Db\Table\RequestConfig;
use Application\Entity\User;
use Application\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Model\Project;
use Zend\File\Transfer\Adapter\Http;

use Application\Model\File;

//use Application\Model\User as ModelUser;

class ProjectController extends AbstractActionController
{
	/* public function getModelUser()
    {
        return $this->getServiceLocator()->get('Model\User');
    } */

    public function createAction()
    {
        $user = $this->getModelUser()->getFromSession();
        $requestConfig = new RequestConfig();
		//$designers = $this->getDesigners();
        if($this->getRequest()->isPost()) {
            $generator = new \Application\Generator\Xml();

            $request = new Request($requestConfig->getByAlias('projectCreate'), $this->getServiceLocator());
            $project = $generator->generateProjectCreate(
                $this->getRequest()->getPost(),
				$this->getDesigners(),
                //$this->getRequest()->getFiles(),
                $user
            );

/* echo "<pre>";
print_r($designers);die(); */

/* header('Content-Type: application/xml'); 
print_r($project);die(); */
            $request->setBody($project);

            if($request->request()->isSuccess()) {
				return new ViewModel(array('createProject' => 'success'));
                /* $user->addCipher($this->getRequest()->getPost()->cipher);
                $this->redirect()->toUrl('/'); */
            }
        }

        return new ViewModel(array(
            'user' => $user,
            'designers' => $this->getModelUser()->getDesignerList(),
        ));
    }

    public function getDesigners()
    {
        $designers = array();
        foreach($this->getModelUser()->getDesignerList() as $designer) {
            $designers[$designer->getId()] = $designer;
        }

        return $designers;
    }

    public function viewAction()
    {
        $user = $this->getModelUser()->getFromSession();
        //$designers = $this->getDesigners();
        $requestConfig = new RequestConfig();
        $config = $this->getServiceLocator()->get('Config');
        $stages = $config['application']['stages'];
		
		if(isset($this->getRequest()->getQuery()->cipher) && !empty($this->getRequest()->getQuery()->cipher)) {
            $cipher = $this->getRequest()->getQuery()->cipher;
			$alias      = $requestConfig->getByAlias('AllStages');
			$reader     = new \Application\XmlReader\ProjectViewForDesigner();
			$request = new Request($alias, $this->getServiceLocator());
			$request->setArgs(array($cipher, $request->getUuid()));
			$response = $request->request();
			$file="C:\Debug\\temp\AllStages_".$cipher."_".$request->getUuid().".xml";
			$xml = (array)simplexml_load_file($file);
			/* foreach($xml[ID] as $id)
				echo $id->attributes[PE]; */
			//$xml = $reader->read($response->getXml());
		
		/* echo "<pre>";
		print_r($xml);
		die(); */
			
			
        /* 
            if($user->isGipRole()) {
                $generator  = new \Application\Generator\Xml();
                $project    = $generator->generateProjectViewForGip($user, $cipher);
                $alias      = $requestConfig->getByAlias('projectViewForGip');
                $reader     = new \Application\XmlReader\Project();
            } else {
                $generator  = new \Application\Generator\Xml();
                $project    = $generator->generateProjectViewForDesigner($user, $cipher);
                $alias      = $requestConfig->getByAlias('projectViewForDesigner');
                $reader     = new \Application\XmlReader\ProjectViewForDesigner();
            }

            $request = new Request($alias, $this->getServiceLocator());
            $request->setBody($project);
            $response = $request->request();
            if($response->isSuccess()) {
                $project = $reader->read($response->getXml());
            }
//echo "<pre>";			
//print_r($project);die();
//print_r($pe);

            $peStages = array();
			//$rand=array();
            $date = new \DateTime();
            $interval = \DateInterval::createFromDateString('+4 days');
            foreach($project->getListPe() as $pe) {
                //$rand = $stages[array_rand($stages)];
 $rand=$this->peStage($cipher,$pe->getNumber(),0);	

//print_r($rand);
                $peStages[$pe->getNumber()] = array_merge(
                    array(
                        'date_time' => $date->format('Y-m-d H:i:s'),
                    ),
                    $rand);
                $date->add($interval);
            }

//print_r($pe);
//print_r($peStages);			
			*/
        }  
		else {
            throw new \ErrorException('Не передан шифр проекта');
        }

        return new ViewModel(compact('stages','xml','cipher'));
    }

	
    /* public function loadFileModelAction()
	{
		print_r($user);
		return false;
		//return new ViewModel(compact('user', 'project', 'designers','userPeNumber'));
	}
	
	public function workNewAction()
    {	
		$user = $this->getModelUser()->getFromSession();
		$cipher = $this->getRequest()->getQuery()->cipher;
        $requestConfig = new RequestConfig();
		$request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
		$generator  = new \Application\Generator\Xml();
        $project    = $generator->generateProjectViewForGip($user, $cipher);
        $request->setBody($project);
        $response = $request->request();
        if($response->isSuccess()) {
            $reader     = new \Application\XmlReader\Project();
            $project = $reader->read($response->getXml());
        }
		$userPeNumber = $project->getUserPe($user)->getNumber();
		$peStage=$this->peStage($cipher,$userPeNumber);
		//echo "<pre>";
		//print_r($peStage);
		//echo "</pre>";
	
		if($peStage[number]<3)
	//		$this->redirect()->toUrl("/application/project/loadFileModel?cipher=$cipher",$cipher);
	//		$this->_helper->_redirector('loadFileModel','project','application',$user);
	$this->getHelper('Redirector')->gotoSimpleAndExit('loadFileModel','project','application',$user);
	} */
	public function workAction()
    {
	    $user = $this->getModelUser()->getFromSession();
        $requestConfig = new RequestConfig();
        $designers = $this->getDesigners();

		$cipher = $this->getRequest()->getQuery()->cipher;
		$peNumber=$this->getRequest()->getQuery()->peNumber;
		
        $generator  = new \Application\Generator\Xml();
        $project    = $generator->generateProjectViewForGip($user, $cipher);
        $request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
        $request->setBody($project);
        $response = $request->request();
        if($response->isSuccess()) {
            $reader     = new \Application\XmlReader\Project();
            $project = $reader->read($response->getXml());
        }
		//$userPeNumber = $project->getUserPe($user)->getNumber();

$userLogin=$user->getLogin();
$UserInfo=$this->getModelUser()->getList();
$MAC=$UserInfo[$userLogin-1]->getMac();

		return new ViewModel(compact('user', 'project', 'designers','peNumber','MAC'));
    }
	
		public function ShowModelAction()
    {
	    $user = $this->getModelUser()->getFromSession();
        $requestConfig = new RequestConfig();
  		$cipher = $this->getRequest()->getQuery()->cipher;
        $generator  = new \Application\Generator\Xml();
        $project    = $generator->generateProjectViewForGip($user, $cipher);
        $request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
        $request->setBody($project);
        $response = $request->request();
        if($response->isSuccess()) {
            $reader     = new \Application\XmlReader\Project();
            $project = $reader->read($response->getXml());
        }
		$userPeNumber = $project->getUserPe($user)->getNumber();
			
			$alias      = $requestConfig->getByAlias('schemaData');
			$request = new Request($alias, $this->getServiceLocator());
			$request->setArgs(array($cipher,2, $request->getUuid()));
			$response = $request->request();
			$file="C:\Debug\\temp\xml1.xml";
			$xml = (array)simplexml_load_file($file);
		
		//Project for scheme
		
		
		return new ViewModel(compact('user', 'project', 'xml','userPeNumber','cipher' ));
    }
	
public function ShowModel1Action()
 {
	$user = $this->getModelUser()->getFromSession();
	if($this->getRequest()->isPost()){
		$request=$this->getRequest()->getFiles();
		$fileModelPath = $request[model][tmp_name];
/* 	echo $fileModelPath;
	die(); */
		//$file=file($fileModelPath);
		$File=new File;
		$drawArray=$File->getArray($fileModelPath);
		return new ViewModel($drawArray);
	}	
	return new ViewModel();
 }
	
public function ShowModel2Action()
    {
	    $user = $this->getModelUser()->getFromSession();
		return new ViewModel();
    }
	
public function ShowModel3Action()
    {
		$cipher = $this->getRequest()->getQuery()->cipher;
/* 	    $user = $this->getModelUser()->getFromSession();
        $requestConfig = new RequestConfig();
       	
        $generator  = new \Application\Generator\Xml();
        $project    = $generator->generateProjectViewForGip($user, $cipher);
		$request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
        $request->setBody($project);

        $response = $request->request();
		if($response->isSuccess()) {
            $reader     = new \Application\XmlReader\Project();
            $project = $reader->read($response->getXml());
        }
		$userPeNumber = $project->getUserPe($user)->getNumber(); */
		
		
		//Project scheme
		$this->schemaData($cipher);
	return new ViewModel(compact('user', 'project', 'cipher','userPeNumber' ));
    }
	
	public function saveModelAction()
	{	
/* $user = $this->getModelUser()->getFromSession();
print_r($this->getRequest()->getFiles());
$key = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];
echo "$key <pre>";
var_dump($_POST);
echo "s\n";
var_dump($_SESSION[$key]);
die(); */
	
		$user = $this->getModelUser()->getFromSession();
		$cipher = $this->getRequest()->getPost()->cipher;
		$peNumber = $this->getRequest()->getPost()->peNumber;
		$MAC = $this->getRequest()->getPost()->MAC;
		//$userPeNumber = $this->getRequest()->getPost()->userPeNumber;
		$request=$this->getRequest()->getFiles();
		$fileModelPath = $request[fileModel][tmp_name];
		
		chdir("C:\Debug");
		
		//$fileModel=str_replace('Z:\\tmp\\','',$fileModelPath);
		//copy($fileModelPath,"C:\\Debug\\$fileModel");
		
		//$result = exec("ConsoleClient.exe file $fileModel $MAC", $output, $status);
		//echo "\n Status: $status\n";
		//if($status==0)
			//$this->redirect()->toUrl("/application/project/commonNodes?cipher=22PE&peNumber=1);
		/* echo "<pre>";
		print_r($result); */
		//
		$amac="a".$MAC;
//echo $amac;die();
		//exec("ConsoleClient.exe file $fileModel $amac", $output, $status);
			//if($status==1){
				$peNewNumber=1;
				$requestConfig = new RequestConfig();
				$request = new Request($requestConfig->getByAlias('updatePeStage'), $this->getServiceLocator());
				$request->setArgs(array($cipher,$peNumber,$peNewNumber, 1));
				$response = $request->request();
			//}
				//$this->redirect()->toUrl("/application/project/peStages?cipher=$cipher");
		return new ViewModel(compact('cipher', 'peNumber'));
	}
	
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
	
	public function SavePointsAction()
	{
		$user = $this->getModelUser()->getFromSession();
		$dictionary = new \Application\Db\Table\Dictionary();
		$bps = $dictionary->getBps();
		
/* 		if($this->getRequest()->isPost())
		{ */
	        /* $data = $_GET;
            $data['bps'] = $bps[$data['bps']]; */
			$data=$this->getRequest()->getPost();
/* echo "<pre>";
print_r($data);
die(); */
			$data['bps'] = $bps[$data['bps']];
			$cipher=$this->getRequest()->getPost()->cipher;
			$userPeNumber=$this->getRequest()->getPost()->common_pe;
            $requestConfig = new RequestConfig();
			$request = new Request($requestConfig->getByAlias('projectCommonNode'), $this->getServiceLocator());
            $generator = new \Application\Generator\Xml();
            $commonNodes = $generator->generateCommonNodes($data);
            $request->setBody($commonNodes);
			$request->request();
			
			$this->updatePeStage($cipher,$userPeNumber,2);
			
            /* if($request->request()->isSuccess()) {
                // @TODO: Редирект?
                //\Zend\Debug\Debug::dump(htmlspecialchars($commonNodes));
				$success=0;
            }
			else
				$success=1; */
			
			//$peStage=$this->peStage($cipher,$userPeNumber,1);
			//return new ViewModel($peStage);
			//$this->redirect()->toUrl("/application/project/peStatus?cipher=$cipher&userPeNumber=$userPeNumber");
			
			/* TODO: проверка состояний и вывод отчёта */
			
		/* }
		else {
			/* $cipher=$this->getRequest()->getQuery()->cipher;
			$userPeNumber=$this->getRequest()->getQuery()->userPeNumber;
				$this->redirect()->toUrl("/application/project/peStatus?cipher=$cipher&userPeNumber=$userPeNumber"); */
		/* } */ 
		
		return new ViewModel(compact('cipher','userPeNumber'));
	}
	
	public function peStatusAction()
	{
		$user = $this->getModelUser()->getFromSession();
		$cipher=$this->getRequest()->getQuery()->cipher;
//echo $cipher;die();
		$userPeNumber=$this->getRequest()->getQuery()->userPeNumber;
		$peStage=$this->peStage($cipher,$userPeNumber,0);
		return new ViewModel($peStage);
	}	
	
	public function commonNodesAction()
    {
			$dictionary = new \Application\Db\Table\Dictionary();
			$bps = $dictionary->getBps();
			$values=$this->commonNodesAction2();
				if($_FILES['xyz-1']['size']!=0){
					$points=$this->readFilesAction();
					$values['points'] = $points;
				return new ViewModel($values);
				exit;
				}	
			return new ViewModel($values);
	}
	
public function readFilesAction()
{
	//echo '<pre>';
	//print_r($_FILES);
	foreach($_FILES as $files)
	{
		$file=file($files['tmp_name']);
		foreach ($file as $string)
		{
			$xyz=explode(';',$string);
			if(preg_match("/^[0-9]/",$xyz[0]))
			{
				//echo "$string\n";
				//foreach($xyz as $x)
					$points[]=array("x"=>$xyz[1],"y"=>$xyz[2],"z"=>$xyz[3]);
			}
		}
	}
//print_r($points);	
//echo '</pre>';
	return $points;
}

	
	public function peStage($cipher,$peNumber, $shift)
    {
//echo "$cipher,$peNumber, $shift";die();	
        $config = $this->getServiceLocator()->get('Config');
        $stages = $config['application']['stages'];
        if($peNumber && $cipher) {
            $requestConfig = new RequestConfig();
            $request = new Request($requestConfig->getByAlias('peStage'), $this->getServiceLocator());
            $request->setArgs(array($cipher, $peNumber, 1));

            $response = $request->request();

		$currentStage = $response->getOutput();
	return $currentStage;
		}
	}
	
		public function agreed($cipher,$peNumber)
    {
//echo "$cipher,$peNumber, $shift";die();	
        //$config = $this->getServiceLocator()->get('Config');
        //$stages = $config['application']['stages'];
        if($peNumber && $cipher) {
            $requestConfig = new RequestConfig();
            $request = new Request($requestConfig->getByAlias('peAgreed'), $this->getServiceLocator());
            $request->setArgs(array($cipher, $peNumber, 1));
            $response = $request->request();
			$currentStage = $response->getOutput();
	return $currentStage;
		}
	}
	
	public function updatePeStage($cipher,$userPeNumber,$peNewNumber)
    {			
		$requestConfig = new RequestConfig();
		$request = new Request($requestConfig->getByAlias('updatePeStage'), $this->getServiceLocator());
		$request->setArgs(array($cipher,$userPeNumber,$peNewNumber, 1));
		$response = $request->request();
	}
	
	private function schemaData($cipher)
	{
		$requestConfig = new RequestConfig();
		$request = new Request($requestConfig->getByAlias('schemaData'), $this->getServiceLocator());
		
/* 		 */
		
		//$request->setArgs(array($requestConfig->getByAlias('schemaData'), $cipher, 1, $request->getUuid()));
		$request->setArgs(array($cipher, 1, $request->getUuid()));
		$response = $request->request();
		
		echo "<pre>";
		print_r($response->getFile());
		die();
	}	

    /** @return \Application\Model\User */
	public function peStagesAction()
    {
	if($this->getRequest()->isPost()){
		$data=$this->getRequest()->getPost();
		if($data[type]==1){
			$peStageNumber=$this->peStage($data[cipher],$data[pe_number],0);
			echo $peStageNumber;
			die();
		}
		if($data[type]==2){
			$agreed=$this->agreed($data[cipher],$data[pe_number]);
			if($agreed != -1){
				$this->updatePeStage($data[cipher],$data[pe_number],3);
				echo "OK";
				}
			die();
		}
	  }
	
	$requestConfig = new RequestConfig();
	 $user = $this->getModelUser()->getFromSession();
	 $cipher = $this->getRequest()->getQuery()->cipher;
	 $peNumber = $this->getRequest()->getQuery()->peNumber;
     $generator  = new \Application\Generator\Xml();
//$cipher = "4PE";
     $project = $generator->generateProjectViewForGip($user, $cipher);
	 $request = new Request($requestConfig->getByAlias('projectWorkForDesigner'), $this->getServiceLocator());
            $request->setBody($project);
            $response = $request->request();
            if($response->isSuccess()) {
                $reader     = new \Application\XmlReader\Project();
                $project = $reader->read($response->getXml());
            }
	// echo "<pre>";
	$designers = $this->getDesigners();
	 //$pe_number = $project->getUserPe($user)->getNumber();
//echo $pe_number;die();
	 /* $peStage=$this->peStage($cipher,$pe_number,0);
	 $peStageNumber=$peStage[number]; */
	 //$peStageNumber=$this->peStage($cipher,$pe_number,0);
	 $config = $this->getServiceLocator()->get('Config');
	 $stages = $config['application']['stages'];
	 return new ViewModel(compact('stages','cipher','user','project','designers','peNumber'));
	 //print_r($peStage);die();
	 //exec();
	 
	 /* $request2 = new Request($requestConfig->getByAlias('peStage'), $this->getServiceLocator());
	 $request2->setArgs(array($cipher,  $pe_number, "1"));
	 $response2 = $request2->request();
	 
	 
	 //$config = $this->getServiceLocator()->get('config');
	 echo $response2->getOutput();
	 //print_r($response2);
	 //print_r($this->getServiceLocator());
	 die(); */
    }
}