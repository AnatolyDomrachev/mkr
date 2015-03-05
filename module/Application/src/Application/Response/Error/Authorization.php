<?php

namespace Application\Response\Error;

use Application\Response\Factory;

class Authorization extends Error
{

    public function getStatus()
    {
        return Factory::ERROR_AUTHORIZATION;
    }

}