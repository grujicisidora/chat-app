
<?php

//fetch_user.php

include('database_connection.php');

session_start();

$query = "
SELECT * FROM login 
WHERE user_id != '".$_SESSION['user_id']."' /* fetchuje podatke svih korisnika osim trenutno ulogovanog(korisnici sa kojima moze da cetuje) */
";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$output = '		
<table class="table table-bordered table-striped">
 <tr>
  <th width="70%">Username</td>
  <th width="20%">Status</td>
  <th width="10%">Action</td>
 </tr>
'; //pravimo tabelu sa usernamom, statusom i opcijom za cetovanje

foreach($result as $row) //fetchujemo podatke iz $result promenljive
{



 $status = '';
 $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 2 second');
 //dobijamo unix time(u sekundama)
 $current_timestamp = date('Y-m-d H:i:s', $current_timestamp); //prebacujemo iz unix time u date & time format
 $user_last_activity = fetch_user_last_activity($row['user_id'], $connect); //dobija user_last_activity preko fetch_user_last_activity f-je definisane u database-connection.php
 if($user_last_activity > $current_timestamp)
 {
  $status = '<span class="label label-success">Online</span>';
 }
 else
 {
  $status = '<span class="label label-danger">Offline</span>';
 }
 $output .= '
 <tr>
  <td>'.$row['username'].' '.count_unseen_message($row['user_id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['user_id'], $connect).'</td>
  <td>'.$status.'</td>	
  <td><button type="button" class="btn btn-info btn-xs start_chat" data-touserid="'.$row['user_id'].'" data-tousername="'.$row['username'].'">Start Chat</button></td>
 </tr>
 ';
}

$output .= '</table>';

echo $output; //send data to ajax function in index.php

?>