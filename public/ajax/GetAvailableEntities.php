<?php

if(isset($_GET['from']))
{
    $from = $_GET['from'];
}

if(isset($_GET['to']))
{
    $from = $_GET['to'];
}

if(isset($_GET['type']))
{
    $from = $_GET['type'];
}
if(isset($date_from) && isset($date_to) && isset($today))
{
    $date_from = strtotime($from);
    $date_to = strtotime($to);
    $date_today = time();    
    
    echo 'Date from: '.date('d.m.Y', $date_from)."<br>";
    echo 'Date to: '.date('d.m.Y', $date_to)."<br>";
    echo 'Date today: '.date('d.m.Y',$date_today)."<br>";
    
    if($date_today > $date_from && $date_today < $date_to)
    {
        echo "Inside!<br><br>";
    }
    else {
        echo "Outside!<br><br>";
    }
}

$conn = pg_connect('host=192.168.0.7 port=5432 dbname=hotel user=hotel password=BiloKoji12');
$result = pg_query($conn, 'SELECT entity.id as index, entity.guid FROM entity 
                           JOIN entity_definition ON (entity_definition.id = entity.definition_id) 
                           JOIN reservations ON (reservations.entity_id = entity.id)
                           ORDER BY entity.id');
$rows = pg_fetch_all($result);
foreach($rows as $row)
{
    foreach($row as $key=>$value)
    {
        echo 'row['.$key.'] = '.$value.'<br>';
    }
    echo "<br><br>";
}
pg_close($conn);


