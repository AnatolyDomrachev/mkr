<?php

namespace Application\Request\Project;

use Application\Request\Request;

class Create extends Request
{

    public function getFileName()
    {
        return 'Project.xml';
    }

    public function request()
    {
        $_SESSION['execute'] = 'projectCreate';
        $_SESSION['requestPath'] = $this->getUserRootPath().'/request/'.$this->getFileName();

        return parent::request();
    }

}