<?php

namespace Pms\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Debug\Debug;

class EntityReportModel
{
    protected $dbAdapter;
    protected $sql;
    
    public function __construct(Adapter $adapter) {
        $this->dbAdapter = $adapter;
        $this->sql = new Sql($this->dbAdapter);
    }
    
    public function getCompleteEntityUsageData($type=null, $start = null, $end = null)
    {
        $table = new \Zend\Db\TableGateway\TableGateway('reservation_status', $this->dbAdapter);
        $rows = $table->select(['statusvalue' => 'checkout']);
        $status_id = $rows->current()['id'];
                
        $select = $this->sql->select();
        $select->from(['r' => 'reservation_entity'])
                ->join(['e' => "entity"], 'r.entity_id=e.id', ['guid'])
                ->join(['ed' => 'entity_definition'], 'e.definition_id=ed.id', ['code','name'])
                ->join(['et' => 'entity_type'], 'ed.entity_type_id=et.id', ['time_resolution'])
                ->join(['res' => 'reservations'], 'r.reservation_id=res.id', ['status_id']);
        $select->where->equalTo('res.status_id', $status_id);
        if($type)
        {
            $select->where->equalTo('et.id', $type);
        }
        if(!empty($start))
        {
            $select->where->greaterThanOrEqualTo('date_from', $start);
        }
        if(!empty($end))
        {
            $select->where->lessThanOrEqualTo('date_to', $end);
        }
        
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $rows = $statement->execute();
        
        $usageData = array();
        $header = array();        
        $etModel = new \Pms\Model\EntityTypeModel($this->dbAdapter);
        $etModel->setId($type);        
        $header['type'] = $etModel->name;
        
        if($start)
        {
            $header['from'] = $start;
        }
        if($end)
        {
            $header['to'] = $end;
        }        
        
        $usageData['header'] = $header;
        
        foreach($rows as $row)
        {
            $guid = $row['guid'];
            
            $starttime = $row['date_from'];
            $endtime = $row['date_to'];
            
            $diffTime = strtotime($endtime) - strtotime($starttime);
            $diffTime /= 3600;                          
            if($row['time_resolution'] > 1/* currently days, in the future maybe something more */)
            {
                $diffTime /= 24;
            }
            
            $entry = array();
            $entry['entity_id'] = $row['entity_id'];
            $entry['guid'] = $guid;
            $entry['code'] = $row['code'];
            $entry['name'] = $row['name'];
            $entry['duration'] = $diffTime;            
            $time_units = ['none', 'hour', 'day'];
            $entry['unit'] = $time_units[$row['time_resolution']];            
            
            if(array_key_exists($guid, $usageData))
            {
                $oldVal = (int)$usageData[$guid]['duration'];
                $val = $diffTime;
                $val += $oldVal;
                $usageData[$guid]['duration'] = $val;                                
            }
            else 
            {
                $usageData[$guid] = $entry;
            }
        }
        
        return $usageData;
    }
    
    public function getSingleEntityUsageData($entity_id, $from = null, $to = null)
    {
        $table = new \Zend\Db\TableGateway\TableGateway('reservation_status', $this->dbAdapter);
        $rows = $table->select(['statusvalue' => 'checkout']);
        $status_id = $rows->current()['id'];
        
        $select = $this->sql->select();
        $select->from(['re' => 'reservation_entity'])
                ->join(['r' => 'reservations'], 're.reservation_id=r.id', ['reservation_id', 'client_id', 'status_id'])
                ->join(['c' => 'clients'], 'r.client_id=c.id', ['Client Firstname' => 'first_name','Client Lastname' =>'last_name'])
                ->join(['g' => 'clients'], 're.guest_id=g.id', ['Guest Firstname' => 'first_name','Guest Lastname' => 'last_name'])
                ->where([
                    're.entity_id' => $entity_id,
                    'r.status_id' => $status_id,
                ]);
        if($from)
        {
            $select->where->greaterThanOrEqualTo('re.date_from', $from);
        }
        
        if($to)
        {
            $select->where->lessThanOrEqualTo('re.date_to', $to);
        }
                        
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $rows = $statement->execute();
        
        $entityUsingData = array();
        
        $entityModel = new \Pms\Model\EntityModel($this->dbAdapter);
        $entityModel->setId($entity_id);
        
        $header = array();
        $header['Number'] = $entityModel->guid;
        $header['Type Code'] = $entityModel->entityDefinitionModel->code;
        $header['Type Name'] = $entityModel->entityDefinitionModel->name;
        $entityUsingData['header'] = $header;
        
        $entity_type_id = $entityModel->entityDefinitionModel->entity_type_id;
        $entityTypeTable = new \Zend\Db\TableGateway\TableGateway('entity_type', $this->dbAdapter);
        $results = $entityTypeTable->select(['id' => $entity_type_id]);
        $time_resolution = $results->current()['time_resolution'];
        $intervals = array('none', 'hours', 'days', 'days');
                
        foreach($rows as $row)
        {
            $entityUsing = array();
            $entityUsing['reservation_id'] = $row['reservation_id'];
            $entityUsing['reserved_by'] = $row['Client Firstname'].' '.$row['Client Lastname'];
            $entityUsing['used_by'] = $row['Guest Firstname'].' '.$row['Guest Lastname'];
            $entityUsing['from'] = $row['date_from'];
            $entityUsing['to'] = $row['date_to'];
            $entityUsing['duration'] = ((int)strtotime($row['date_to']) - (int)strtotime($row['date_from']))/($time_resolution == 1 ? 3600 : (3600 * 24));
            $entityUsing['time_unit'] = $intervals[$time_resolution];
            $entityUsingData[$row['reservation_id']] = $entityUsing;
        }
        
        return $entityUsingData;
    }
    
}

