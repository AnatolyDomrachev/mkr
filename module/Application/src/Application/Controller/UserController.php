<?php

namespace Application\Controller;

use Application\Db\Table\RequestConfig;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\User;
use Application\Request;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{

    public function loginAction()
    {
        if($this->getModelUser()->isInSession()) {
            $this->redirect()->toUrl('/');
            return false;
        }

        $error_message = '';
        if($this->getRequest()->isPost()) {
            $user = new User();
            $user->setLogin($this->getRequest()->getPost()->login);
            $user->setPassword($this->getRequest()->getPost()->password);
            $user->setMode($this->getRequest()->getPost()->mode);

            if($user->getLogin() && $user->getPassword() && $user->getMode()) {
                $generator = new \Application\Generator\Xml();
                $requestConfig = new RequestConfig();

                $request = new Request($requestConfig->getByAlias(Request::TYPE_AUTHORIZATION), $this->getServiceLocator());
                $request->setBody($generator->generateAuthorization($user, $this->getModelUser()));
                $response = $request->request();
                if($response->isSuccess()) {
                    $reader = new \Application\XmlReader\Authorization();
                    $reader->read($response->getXml(), $user);

                    if($user->getFio() && $user->getId() && $user->getRole()) {
                        if($user->isDesignerRole() && $user->isSingleMode()) {
                            $error_message = 'Нет прав на однопользовательский режим';
                        } else {
                            $this->getModelUser()->writeToSession($user);
                            $this->redirect()->toUrl('/application');
                            return false;
                        }
                    }
                }
            }

            if(empty($error_message)) {
                $error_message = 'Авторизация не прошла';
            }
        }

        return new ViewModel(compact('error_message'));
    }

    public function panelAction()
    {
        if(!$this->getModelUser()->isInSession()) {
            $this->redirect()->toUrl('/application/user/login');
            return false;
        }

        return new ViewModel(array(
            'user' => $this->getModelUser()->getFromSession(),
        ));
    }

    public function logoutAction()
    {
        $this->getModelUser()->removeFromSession();
        $this->redirect()->toUrl('/application/user/login');
    }

	public function saveUsersAction()
	{
/* 	$data=$this->getRequest()->getPost()->users;
	$users=$this->getModelUser()->update($data);
	echo "<pre>";print_r($users);echo "</pre>";die(); */
		if($this->getRequest()->isPost()) {
			//
			$data=$this->getRequest()->getPost()->users;
			$users=$this->getModelUser()->update($data);
		    $generator = new \Application\Generator\Xml();
            $requestConfig = new RequestConfig();
            $request = new Request($requestConfig->getByAlias('updateUsers'), $this->getServiceLocator());
		//echo "<head>header('Content-Type: application/xml')</head>";
		//echo $generator->generateUpdateUser($users);die();
            $request->setBody($generator->generateUpdateUser($users));
            $response = $request->request();
            if($response->isSuccess()) {
				echo "OK";die();
			}
		}
	}
	
    public function listAction()
    {

//echo "<pre>";
//print_r($this->getModelUser()->getList());
		
        return new ViewModel(array(
            'users' => $this->getModelUser()->getList(),
        ));
    }

    /** @return \Application\Model\User */
    public function getModelUser()
    {
        return $this->getServiceLocator()->get('Model\User');
    }

}