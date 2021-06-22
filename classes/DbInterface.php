<?php 
declare(strict_types=1);

//namespace TeamPlanning\Classes\Db\Repository;

interface DbInterface
{
    public function setLog($bln);

    public function getLog();
    
    public function connect($conInfos, $no_msg = 0);

    public function execQuery($link, $query);

    public function execPreparedQuery($link, $query, $args=null, $again);

    public function fetchRow($resultSet);

    public function numRows($resultSet);

    public function fetchArray($resultSet); 
    
    public function fetchAssoc($resultSet);

    public function escapeString($link, $arg);


}