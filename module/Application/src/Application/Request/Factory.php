<?php

namespace Application\Request;

use Application\Entity\User;
use Application\Request\Project;

class Factory
{

    const AUTHORIZATION = 'authorization';
    const USERS = 'users';
    const PROJECT_CREATE = 'projectCreate';
    const PROJECT_GET = 'projectGet';

    /**
     * @return Request
     */
    public static function factory($type, User $user)
    {
        switch($type)
        {
            case self::AUTHORIZATION:
                $request = new Authorization();
                break;
            case self::USERS:
                $request = new Users();
                break;
            case self::PROJECT_CREATE:
                $request = new Project\Create();
                break;
            case self::PROJECT_GET:
                $request = new Project\Get();
                break;
            default:
                throw new \ErrorException("Unknown request type ($type)");
        }

        $request->setUser($user);
        return $request;
    }
}