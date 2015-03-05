<?php

namespace Application\Controller;

use Application\Db\Table\RequestConfig;
use Application\Entity\User;
use Application\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Model\File;

class TestController extends AbstractActionController
{

public function editAction() {
   if($this->getRequest()->isPost()) {
            // @TODO: Сохранение
			return new ViewModel($file);
}
}
}
