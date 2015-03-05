<?php

namespace Application\Controller;

use Application\Db\Table\Config;
use Application\Db\Table\Dictionary;
use Application\Db\Table\RequestConfig;
use Application\Db\Table\ResponseStatus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ConfigController extends AbstractActionController
{

    public function indexAction()
    {
        $dictionaryTable = new Dictionary();
        $configTable = new Config();
        $statusesTable = new ResponseStatus();
        $requestConfigTable = new RequestConfig();

        $config = $this->getServiceLocator()->get('config');
        $modes = $dictionaryTable->getByType('mode');
        $currentMode = $configTable->getByKey('mode');
        $clientFile = $configTable->getByKey('clientFile');

        return new ViewModel(array(
            'config' => $config['application'],
            'modes' => $modes,
            'currentMode' => $currentMode,
            'clientFile' => $clientFile,
            'responseStatuses' => $statusesTable->getAll(),
            'requestConfig' => $requestConfigTable->getAll(),
        ));
    }

    public function clientAction()
    {
        return new ViewModel();
    }

    public function updateKeyAction()
    {
        if($this->getRequest()->isPost()
            && isset($this->getRequest()->getPost()->key)
            && isset($this->getRequest()->getPost()->value)
            && !empty($this->getRequest()->getPost()->key))
        {
            $dictionary = new Config();
            $dictionary->updateKey($this->getRequest()->getPost()->key, $this->getRequest()->getPost()->value);
        }
        exit;
    }

    public function updateFunctionAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $config = new RequestConfig();
            $config->updateAlias($post->alias, $post->number, $post->requestFile, $post->responseFile);
        }
        exit;
    }

    public function updateResponseCodeAction()
    {
        if($this->getRequest()->isPost()
            && isset($this->getRequest()->getPost()->alias)
            && isset($this->getRequest()->getPost()->value)
            && !empty($this->getRequest()->getPost()->alias))
        {
            $statuses = new ResponseStatus();
            $statuses->updateAlias($this->getRequest()->getPost()->alias, $this->getRequest()->getPost()->value);
        }
        exit;
    }

}