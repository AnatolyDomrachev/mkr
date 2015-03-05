<?php

namespace Application\Controller;

use Application\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function mainAction()
    {
        return new ViewModel(array(
            'user' => $this->getModelUser()->getFromSession(),
        ));
    }

    public function detailsAction()
    {
        return new ViewModel();
    }

    public function statusAction()
    {
        return new ViewModel();
    }

    /** @return \Application\Model\User */
    public function getModelUser()
    {
        return $this->getServiceLocator()->get('Model\User');
    }
}