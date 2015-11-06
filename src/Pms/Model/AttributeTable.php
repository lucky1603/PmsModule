<?php
/**
 * @name AttributeTable.php
 * @description Object model of the AttributeTable object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 05.11.2015.
 */

namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;

/**
 * Attribute table helper class.
 */
class AttributeTable 
{
    /**
     * Table gateway helper member.
     * @var type 
     */
    protected $tableGateway;
    
    /**
     * Constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Saves attribute to the database.
     * @param \Pms\Model\Attribute $attribute
     * @throws Exception
     */
    public function saveAttribute(Attribute $attribute)
    {
        $data = [
            'id' => $attribute->id,
            'code' => $attribute->name,
            'label' => $attribute->label,
            'type' => $attribute->type,
            'sort_order' => $attribute->sort_order,
        ];
        
        $id = (int)$attribute->id;
        
        if($id == 0) {
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getAttribute($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else {
                throw new \Zend\Db\Exception("User IO doesn't exist!");
            }
        }    
    }
    
    /**
     * Gets attribute with the given id.
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function getAttribute($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(['id' => $id]);
        $row = $resultSet->current();
        if(!$row)
        {
            throw new Exception("No attribute with id = " . $id);
        }        
        return $row;
    }
    
    /**
     * Fetch all attributes.
     * @return type
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function deleteAttribute()
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}

