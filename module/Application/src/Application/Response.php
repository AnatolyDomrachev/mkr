<?php

namespace Application;

use Application\Db\Table\ResponseStatus;
use Application\Request;

class Response
{

    private $output;
    private $request;
    private $statuses;

    public function __construct($output, Request $request)
    {
        $this->statuses = new ResponseStatus();
        $this->output   = $output;
        $this->request  = $request;
    }

    public function getStatus()
    {
        return $this->statuses->getByValue($this->getOutput());
    }

    public function getXml()
    {
        $file = $this->getFile();
        return @file_get_contents($file);
    }
	
	    public function getXml2($file)
    {
        //$file = $this->getFile();
        return @file_get_contents($file);
    }

    public function getFile()
    {
        $config = $this->request->getSm()->get('config');
        $name   = $this->request->getConfig()->response_file;
        $fileName = sprintf($config['application']['build_file_name'], $name, $this->request->getUuid());

        return $config['application']['response_path'].'/'.$fileName;
    }

    public function isError()
    {
        return $this->getStatus() == 'error' || ($this->isNeedFile() && $this->getXml() === false);
    }

    public function isSuccess()
    {
        return $this->getStatus() == 'success' && (!$this->isNeedFile() || $this->getXml() !== false);
    }

    public function isErrorAuthorization()
    {
        return $this->getStatus() == 'error_authorization';
    }

    protected function isNeedFile()
    {
        return isset($this->request->getConfig()->response_file_name);
    }

    public function getOutput()
    {
        return $this->output;
    }

}