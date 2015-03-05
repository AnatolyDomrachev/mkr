<?php

namespace Application\Request;

class Authorization extends Request
{

    public function getFileName()
    {
        return 'Authorization.xml';
    }

    public function getResponseFileName()
    {
        return 'Role.xml';
    }

    public function request()
    {
        $_SESSION['execute'] = 'authorization';
        $_SESSION['requestPath'] = $this->getUserRootPath().'/request/'.$this->getFileName();
        $_SESSION['responsePath'] = $this->getUserRootPath().'/response/'.$this->getResponseFileName();

        return parent::request();
    }

}