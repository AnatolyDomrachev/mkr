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

class AjaxController extends AbstractActionController
{
	/* public function getModelUser()
    {
        return $this->getServiceLocator()->get('Model\User');
    } */

    public function schemaAction()
    {		$data=$this->getRequest()->getQuery();
			$cipher=$data[cipher];
			$peNumber = $data[pe_number];
			$requestConfig = new RequestConfig();
			$alias      = $requestConfig->getByAlias('schemaData');
			$request = new Request($alias, $this->getServiceLocator());
			$request->setArgs(array($cipher,2, $request->getUuid()));
			$response = $request->request();
			$file="C:\Debug\\temp\xml1.xml";
			$xml = (array)simplexml_load_file($file);
			echo "<pre>";
			print_r($xml);
/* 			foreach($xml[ListPE]->ID[$peNumber-1]->UnitDisplacement->N as $N)
				$arrayTmp[]=(array($N[0]));
			foreach($arrayTmp as $N);
				$arrayTmp2[]=(array)($N[0]);
				print_r($arrayTmp2[0][Load]); */
			
			die();
			//return new ViewModel(compact('xml' ));
	
/* 		//
		$data=$this->getRequest()->getPost();
		//
		echo "<h1> ";
		//echo 1;
		 */
	}
}