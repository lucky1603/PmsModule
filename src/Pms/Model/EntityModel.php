<?php
/**
 * @name        EntityModel.php
 * @decription  Enables manipulation with all of the features conected to the 'entity' object
 *              as well as preview of the values belonging to the connected objects.
 * @author      Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date        12.11.2015.
 */

namespace Pms\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * EntityModel class.
 */
class EntityModel
{
    public $id;
    public $guid;
    public $status;
    public $definition_id;
    public $status_id;
    public $entityDefinitionModel;
    
    protected $dbAdapter;
    protected $sql;
    
    /**
     * Constructor
     * @param Adapter $dbAdapter
     * @param type $id
     */
    public function __construct(Adapter $dbAdapter, $id=NULL) {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
        if(isset($id))
        {
            $this->setId($id);
        }
    }
    
    /**
     * Sets Id and initializes stuff connected with that id.
     * @param type $id
     */
    public function setId($id)
    {
        if(!isset($id))
        {
            return;
        }

        $id = (int)$id;
        $select = $this->sql->select();
        $select->from(['a' => 'entity'])
                ->join(['e' => 'entity_definition'], 'a.definition_id = e.id', ['code'])
                ->join(['s' => 'status'], 'a.status_id = s.id', ['SValue' => 'value'])
                ->where(['a.id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->current();

        // Set local data.
        $this->setData($data);
        
        // Update definition model.
        $this->getDefinitionModel()->setId($this->definition_id);
    }
    
    /**
     * Sets the model data from outside.
     * @param type $data
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = (int)$data['id'];
        }
        
        $this->definition_id = isset($data['definition_id']) ? $data['definition_id'] : null;
        $this->guid = isset($data['guid']) ? $data['guid'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->status_id = isset($data['status_id']) ? $data['status_id'] : null;        
    }
    
    /**
     * Returns the model data.
     * @return type
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'definition_id' => $this->definition_id,
            'guid' => $this->guid,
            'status' => $this->status,
            'status_id' => $this->status_id,
        ];
    }
    /**
     * Returns the model of the belonging definition type.
     * @return type
     */
    public function getDefinitionModel()
    {
        if(isset($this->entityDefinitionModel))
        {
            return $this->entityDefinitionModel;
        }     
        
        $this->entityDefinitionModel = new EntityDefinitionModel($this->dbAdapter);
        if(isset($this->definition_id))
        {
            $this->entityDefinitionModel->setId($this->definition_id);
        }  
        
        return $this->entityDefinitionModel;
    }
    
    // Model interface for binding with forms.
    public function exchangeArray($data)
    {
        $this->setData($data);
    }
    
    public function getArrayCopy()
    {
        return $this->getData();
    }
}