<?php
require 'config.php';

$table=$_GET['table'];
$num=$_GET['num'];
$query=(isset($_GET['query']))?$_GET['query']:'';
if(!empty($query)){
    $sql_query = "";
    foreach ($query as $key => $val) {   
        if(empty($sql_query)){
             $sql_query = " `$key` LIKE '%$val%' ";
        }else{
            $sql_query .= "&& `$key` LIKE '%$val%' ";
        }
    }
    $sql = "SELECT * FROM `$table` WHERE ".$sql_query;
}else{
    $sql = "SELECT * FROM `$table` LIMIT $num";
}
$count=0;
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) :
            $count+=1;
            if ($count >$num)    {
                break;
            }
            ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$row['name']?></td>
                <td><?=$row['name_zh']?></td>
                <td><?=$row['table_name']?></td>
            <td><?=$row['table_name_zh']?></td>
            <td><?=$row['type']?></td>
            <td><?=$row['description']?></td>
            <td><?=($row['key'])?'Y':''?></td>
            <td><?=($row['fk'])?'Y':''?></td>
            <td><?=$row['default']?></td>
            <td><?=$row['remark']?></td>
        </tr>    
            <?php endforeach; ?>