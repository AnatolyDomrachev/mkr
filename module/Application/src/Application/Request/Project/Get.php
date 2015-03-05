<?php

namespace Application\Request\Project;

use Application\Request\Request;

class Get extends Request
{

    public function getFileName()
    {
        return 'Project.xml';
    }

    public function request($cipher)
    {
        $_SESSION['execute'] = 'projectGet';
        $_SESSION['cipher'] = $cipher;
        $_SESSION['responsePath'] = $this->getUserRootPath().'/request/'.$this->getFileName();

        return parent::request();
    }

}