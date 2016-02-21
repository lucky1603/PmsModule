<?php

namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;
use Zend\Db\Sql\Select;

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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country,
            'pcode' => $user->pcode,
            'phone' => $user->phone,
          
        );
        $id = (int) $user->id;
        if($id == 0) {
            // upisi user-a
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getUser($id))
            {
                // Update-uj user-a
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
        // Daj user-a po id-u
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
        // Daj sve user-e
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function fetchView($id=NULL)
    {
        // Approach 1
        $dbAdapter = $this->tableGateway->getAdapter();        
        // Approach 2
        $sql = new \Zend\Db\Sql\Sql($dbAdapter);
        $select = $sql->select();
        $select->from(array('u'  => 'user'))
                ->columns(['id', 'username', 'email'])
                ->join(array('r' => 'role'), 'u.role_id = r.id', ['rolename' => 'name']);
        if($id != NULL)
        {
            $select->where(array('u.id' => $id));
        }                
                                        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results1 = $statement->execute();
        
        $rows = array();
        do {
            $rows[] = $results1->current();
        } while($results1->next());
        
        return $rows;
    }
    
    public function getUserByEmail($userMail)
    {
        // Daj user-a po mail-u
        $resultSet = $this->tableGateway->select(['email' => $userMail]);
        $row = $resultSet->current();
        if(! $row) {
            throw new Exception("Couldn't find row with " . $userMail . " mail.");
        }
        return $row;
    }
    
    public function getUserByRole($role_id)
    {
        $resultSet = $this->tableGateway->select(['role_id' => $role_id]);
        if($resultSet->count() == 0)
        {
            throw new Exception("Couldn't find user with role " . $role_id . ".");
        }
        return $resultSet->toArray();
    }
    
    public function deleteUser($id)
    {
        // izbrisi user-a
        $this->tableGateway->delete(['id' => $id]);
    }
}
