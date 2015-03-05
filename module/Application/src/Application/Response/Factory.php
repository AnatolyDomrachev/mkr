<?php

namespace Application\Response;

use Application\Entity\User;
use Application\Response\Error\Authorization;
use Application\Response\Error\Error;

class Factory
{
    const SUCCESS = 1;
    const ERROR = 10;
    const ERROR_AUTHORIZATION = 20;

    /**
     * @return Response
     */
    public static function factory($status, User $user)
    {
        switch($status)
        {
            case self::SUCCESS:
                $response = new Success();
                break;
            case self::ERROR:
                $response = new Error();
                break;
            case self::ERROR_AUTHORIZATION:
                $response = new Authorization();
                break;
            default:
                throw new \ErrorException("Unknown response status ($status)");
        }
        $response->setUser($user);
        return $response;
    }

}