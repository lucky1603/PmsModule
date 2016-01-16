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
    
    public function getEntityUsageData()
    {
        
    }
}

