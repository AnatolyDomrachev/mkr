<?php

namespace Application\Response;

abstract class Response
{

    protected $user;

    abstract public function isError();
    abstract public function isSuccess();
    abstract public function getStatus();

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserRootPath()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/data/usersData/'.$this->getUser()->getLogin();
    }

}