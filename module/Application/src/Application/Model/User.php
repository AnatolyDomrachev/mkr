<?php

namespace Application\Model;

use Application\Db\Table\RequestConfig;
use Application\Entity\User as EntityUser;
use Application\Request;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class User
{

    private $sm;

    public function __construct(ServiceLocatorInterface $sm)
    {
        $this->sm = $sm;
    }

    public function getSm()
    {
        return $this->sm;
    }

    /**
     * @return EntityUser[]
     */
    public function getList()
    {
        $users = array();
        $requestConfig = new RequestConfig();
        $request = new Request($requestConfig->getByAlias('users'), $this->getSm());
        $response = $request->request();
        if($response->isSuccess()) {
            $reader = new \Application\XmlReader\Users();
            foreach($reader->read($response->getXml()) as $userData) {
                $user = new EntityUser();
                $user->setId($userData['id']);
                $user->setFio($userData['fio']);
                $user->setRole($userData['role']);
                $user->setIp($userData['ip']);
                $user->setMac($userData['mac']);
                $user->setLogin($userData['login']);
                $user->setDescription($userData['description']);
				$user->setPassword($userData['password']);

                $users[] = $user;
            }
        }
        return $users;
    }

    public function hashPassword(\Application\Entity\User $user)
    {
        $config = $this->getSm()->get('Config');
        $request = $config['application']['response_function'];

        $requestConfig = new RequestConfig();

        return $request($requestConfig->getNumberByAlias('hashString'), array($user->getPassword()));
    }
	
	public function aes($password)
    {
        $config = $this->getSm()->get('Config');
        $request = $config['application']['response_function'];

        $requestConfig = new RequestConfig();

        return $request($requestConfig->getNumberByAlias('hashString'), array($password));
    }

    /**
     * @return \Application\Entity\User[]
     */
    public function getDesignerList()
    {
        $designers = array();
        foreach($this->getList() as $user) {
            if($user->isDesignerRole()) {
                $designers[] = $user;
            }
        }
        return $designers;
    }

    public function isInSession()
    {
        $session = new Container('mkr');
        return isset($session->user) && !empty($session->user) && is_object($session->user);
    }

    /**
     * @return \Application\Entity\User
     */
    public function getFromSession()
    {
        $session = new Container('mkr');
        return $session->user;
    }

    public function writeToSession(\Application\Entity\User $user)
    {
        $session = new Container('mkr');
        $session->user = $user;
    }

    public function removeFromSession()
    {
        $session = new Container('mkr');
        unset($session->user);
    }
	
	public function update($users)
	{
		foreach($users as $id=>$user){
			if(isset($user[remove]))
				unset($users[$id]);
			if(isset($user['newPassword'])){
				$users[$id]['Password']=$this->aes($user[newPassword]);
				unset($users[$id]['newPassword']);
			}	
		}
		return($users);	
		//return $this->aes('123');
	}

}