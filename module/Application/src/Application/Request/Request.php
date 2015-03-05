<?php

namespace Application\Request;

use Application\Entity\User;

abstract class Request
{

    private $body;
    private $user;

    abstract protected function getFileName();

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function save()
    {
        $userRoot = $this->getUserRootPath();
        $this->initUserDir($userRoot);

        return file_put_contents($userRoot.'/request/'.$this->getFileName(), $this->getBody()) !== false;
    }

    public function request()
    {
        return \Application\Response\Factory::factory(require "{$this->getRootPath()}/data/script.php", $this->getUser());
    }

    protected function getRootPath()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    }

    protected function getUserRootPath()
    {
        return $this->getRootPath().'/data/usersData/'.$this->getUser()->getLogin();
    }

    protected function initUserDir($userRootPath)
    {
        if(!file_exists($userRootPath)) {
            mkdir($userRootPath);
            mkdir($userRootPath.'/request/');
            mkdir($userRootPath.'/response/');
        }
    }

}