<?php

namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;

class UserTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function saveUser(User $user)
    {
        $data  = array(
          'email' => $user->email,
          'username' => $user->username,
          'password' => $user->password, 
          'role_id' => $user->role_id,
        );
        $id = (int) $user->id;
        if($id == 0) {
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getUser($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else 
            {
                throw new \Exception('User ID doesn\'t exist!');
            }
        }
    }
    
    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if(!$row) {
            throw new \Zend\Db\Exception('Could not find row ' . $id);
        }
        return $row;
    }
    
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getUserByEmail($userMail)
    {
        $resultSet = $this->tableGateway->select(['email' => $userMail]);
        $row = $resultSet->current();
        if(! $row) {
            throw new Exception("Couldn't find row with " . $userMail . " mail.");
        }
        return $row;
    }
    
    public function deleteUser($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
