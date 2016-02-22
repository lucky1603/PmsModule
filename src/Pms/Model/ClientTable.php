<?php
/**
 * @name ClientTable. Mapping to the client table in the database.
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 16.11.2015.
 */
namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Pms\Model\Client;

/**
 * ClientTable class.
 */
class ClientTable
{
    protected $tableGateway;
    
    /**
     * Constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Saves the client to the table.
     * @param Client $client
     * @throws \Exception
     */
    public function saveClient(Client $client)
    {
        $data = [
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'address1' => $client->address1,
            'address2' => $client->address2,
            'city' => $client->city,
            'zipcode' => $client->zipcode,
            'country' => $client->country,
            'phone' => $client->phone,
            'mobile' => $client->mobile,
            'fax' => $client->fax,
            'email' => $client->email,
            'title' => $client->title,
            'guest_class' => $client->guest_class,
            'user_id' => $client->user_id,
        ];
        
        $id = (int) $client->id;
        if($id == 0)
        {
            $this->tableGateway->insert($data);
        }
        else 
        {
            if($this->getClient($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else 
            {
                throw new \Exception("Client doesn't exist!");
            }
        }
    }
    
    /**
     * Gets the client with the given id.
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function getClient($id)
    {
        $id = (int)$id;
        $resultset = $this->tableGateway->select(['id' => $id]);
        $row = $resultset->current();
        if(!$row)
        {
            throw new Exception('Client '.$id.' not found!');
        }
        return $row;
    }
    
    /**
     * Fetch all rows.
     * @return type
     */
    public function fetchAll($user_id=NULL)
    {
        if($user_id == NULL)
        {
            $resultset = $this->tableGateway->select();
        }
        else 
        {
            $resultset = $this->tableGateway->select(['user_id' => $user_id]);
        }
        
        return $resultset;
    }
    
    /**
     * Deletes the client with the given id.
     * @param type $id
     */
    public function deleteClient($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
    
    public function getLastId()
    {        
        $adapter = $this->tableGateway->getAdapter();        
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('clients')
                ->order('id DESC')
                ->limit(1);
        $statement = $sql->prepareStatementForSqlObject($select);
        $rows = $statement->execute();        
        return $rows->current()['id'];
    }
}

