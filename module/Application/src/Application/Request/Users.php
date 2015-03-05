<?php

namespace Application\Request;

class Users extends Request
{

    public function getFileName()
    {
        return 'Users.xml';
    }

    public function request()
    {
        $_SESSION['execute'] = 'users';
        $_SESSION['responsePath'] = $this->getUserRootPath().'/response/Users.xml';

        return parent::request();
    }

}