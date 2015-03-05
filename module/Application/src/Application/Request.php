<?php

namespace Application;

use Zend\ServiceManager\ServiceLocatorInterface;

class Request
{
    const TYPE_AUTHORIZATION = 'authorization';
    const TYPE_USERS = 'users';
    const TYPE_COMMON_NODES = 'commonNodes';
    const TYPE_PROJECT_CREATE = 'projectCreate';

    private $body;

    private $sm;
    private $uuid;
    private $args = array();
	
	private $filename;
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function setFilename($filename)
	{
		$this->filename=$filename;
	}

    public function __construct(array $config, ServiceLocatorInterface $sm)
    {
        $this->sm = $sm;
        $this->config = (object) $config;
        $this->uuid = $this->generateUuid();

        $this->args = array($this->getUuid());
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    protected function getFunctionNumber()
    {
        return $this->getConfig()->number;
    }

    public function getRequestFileName()
    {
        return $this->getConfig()->request_file;
    }

    public function getSm()
    {
        return $this->sm;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function request()
    {
        $result = true;
        $isExistXml = !is_null($this->getBody());

        if($isExistXml) {
            $result = file_put_contents($this->getFile(), $this->getBody());
        }

        if($result != false) {
            $config  = $this->getSm()->get('config');
            $execute = $config['application']['response_function'];
            $result = $execute($this->getFunctionNumber(), $this->getArgs()); //вызов клиента и выполнение запроса, см. global.php
        }

        $response = new \Application\Response($result, $this);
        if(isset($this->getConfig()->response_file) && $response->getXml() === false) {
            //$_SESSION['flush_error'] = "Невозможно прочитать файл ответа ({$response->getFile()})";
        }
        return $response;
    }

    protected function getArgs()
    {
        return $this->args;
    }

    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    protected function getFile()
    {
        $config = $this->getSm()->get('config');
        $fileName = sprintf($config['application']['build_file_name'], $this->getRequestFileName(), $this->uuid);
        return $config['application']['request_path']. '/' . $fileName;
    }

    public function getConfig()
    {
        return $this->config;
    }

    protected function generateUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

}