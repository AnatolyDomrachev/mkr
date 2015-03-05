<?php

namespace Application\Response\Error;

use Application\Response\Factory;

class Error extends \Application\Response\Response
{

    public function isError()
    {
        return true;
    }

    public function isSuccess()
    {
        return false;
    }

    public function getStatus()
    {
        return Factory::ERROR;
    }

}