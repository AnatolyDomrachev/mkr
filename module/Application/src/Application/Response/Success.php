<?php

namespace Application\Response;

class Success extends Response
{

    public function isError()
    {
        return false;
    }

    public function isSuccess()
    {
        return true;
    }

    public function getStatus()
    {
        return Factory::SUCCESS;
    }

    public function getXml() {
        return file_get_contents($_SESSION['responsePath']);
    }

}