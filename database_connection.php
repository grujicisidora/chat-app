<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat4;charset=utf8mb4", "root", "");


date_default_timezone_set('Europe/Belgrade');

function fetch_user_last_activity($user_id, $connect)
{
  //fetchuje poslednje insertovane(za najskorije logovanje) podatke iz login_details tabele
 $query = " 
 SELECT * FROM login_details 
 WHERE user_id = '$user_id' 
 ORDER BY last_activity DESC 
 LIMIT 1
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 foreach($result as $row)
 {
  return $row['last_activity']; //vraca last_activity data za korisnika sa user_id = '$user_id'
 }
}

//fetch-uje konverzaciju izmedju dva korisnika)
function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
 $query = "
 SELECT * FROM chat_message 
 WHERE (from_user_id = '".$from_user_id."' 
 AND to_user_id = '".$to_user_id."') 
 OR (from_user_id = '".$to_user_id."' 
 AND to_user_id = '".$from_user_id."') 
 ORDER BY timestamp DESC
 ";
 $statement = $connect->prepare($query);
 $statement->execute();

 //cuvamo zajednicke poruke u $result
 $result = $statement->fetchAll();
 $output = '<ul class="list-unstyled">';
 foreach($result as $row)
 {

  $user_name = '';
  if($row["from_user_id"] == $from_user_id)
  {
    //ako je poruku poslao ulogovani korisnik, pisace You
    if($row["status"] == '2') //proverava se da li je kliknuto na brisanje poruke
    {
        $chat_message_id=$row['chat_message_id'];

       $query = "DELETE FROM chat_message WHERE chat_message_id =  $chat_message_id";

      $statement = $connect->prepare($query);
      $statement->execute(); 

      //$chat_message = '<em>Message has beesn removed</em>';
     
     /* $_SESSION['message']="Record has been deleted!";
        //$chat_message = '<em>This message has beesn removed</em>';
        $user_name = '<b class="text-success">You</b>';
        /* $connect->query("DELETE FROM chat_message WHERE chat_message_id =  $chat_message_id") or die($connect->error()); */
  }
   else
    {

        $chat_message = $row['chat_message'];
        //dugme za brisanje poruka koje je poslao ulogovan korisnik
        $user_name = '<button type="button"">x</button>&nbsp;<b class="text-success">You</b>';
    }
   
  }
  else
  {
    //ako je poruka primljena, bice prikazano ime tog korisnika
   /* if($row["status"] == '2') //proverava se da li je kliknuto na brisanje poruke
    {
        $chat_message = '<em>This message has beesn removed</em>';
    }
    else
    {
        $chat_message = $row['chat_message'];
    } */
    $chat_message = $row['chat_message'];
   $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
  } 

  //PORUKA
  //Pisace username onoga ko je poslao poruku ili You ako smo mi poslali poruku, sadrzaj poruke i vreme slanja
  $output .= '
  <li style="border-bottom:1px dotted #ccc; padding-top:8px; padding-right:8px;">
   <p>'.$user_name.' - '.$chat_message.'
    <div align="right">
        - <small> <em>'.$row['timestamp'].'</em></small>
    </div>
   </p>
  </li>
  ';

 }
 //$output .= '</ul>'; 


 //apdejtujemo status poruke sa unseen na seen(sa 1 na 0) tamo gde su otvoreni prozori
 $query = " 
 UPDATE chat_message 
 SET status = '0' 
 WHERE from_user_id = '".$to_user_id."' 
 AND to_user_id = '".$from_user_id."' 
 AND status = '1'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $output;
}

//vraca username korisnika sa kojim se dopisujemo kada smo ulogovani da bi smo videli ko salje poruke
//za funkciju fetch_user_chat_history
function get_user_name($user_id, $connect)
{
 $query = "SELECT username FROM login WHERE user_id = '$user_id'";
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 foreach($result as $row)
 {
  return $row['username'];
 }
}

function count_unseen_message($from_user_id, $to_user_id, $connect)
{
 $query = "
 SELECT * FROM chat_message 
 WHERE from_user_id = '$from_user_id' 
 AND to_user_id = '$to_user_id' 
 AND status = '1'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 $count = $statement->rowCount(); //broji poruke koje nisu otvorene
 $output = '';
 if($count > 0) //ukoliko poostoji neka neotvorena poruka, vraca broj tih poruka
 {
  $output = '<span class="label label-success">'.$count.'</span>';
 }
 return $output; //vraca broj neotvorenih poruka
}

//vraca is_type_status za odredjenog korisnika
function fetch_is_type_status($user_id, $connect)
{
 $query = "
 SELECT is_type FROM login_details 
 WHERE user_id = '".$user_id."' 
 ORDER BY last_activity DESC 
 LIMIT 1
 "; 
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 $output = '';
 foreach($result as $row)
 {
  if($row["is_type"] == 'yes')
  {
   $output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
  }
 }
 return $output;
}

//database_connection.php

/* preko to_user_id=0 prepoznajemo poruke iz grupnog ceta */
function fetch_group_chat_history($connect){

 $query = "
 SELECT * FROM chat_message 
 WHERE to_user_id = '0'     
 ORDER BY timestamp DESC
 ";

 $statement = $connect->prepare($query);

 $statement->execute();

 $result = $statement->fetchAll();

 $output = '<ul class="list-unstyled">';
 foreach($result as $row)
 {
  $user_name = '';
  $chat_message='';

  //ako smo mi poslali poruku u grupni cet pisace You
  if($row["from_user_id"] == $_SESSION["user_id"])
  {
      if($row["status"] == '2')
      {
          $chat_message_id=$row['chat_message_id'];

       $query = "DELETE FROM chat_message WHERE chat_message_id =  $chat_message_id";

      $statement = $connect->prepare($query);
      $statement->execute();    
      }
      else{
        $chat_message = $row["chat_message"];
      }
      //x dugme za brisanje poruka koje smo mi poslali u grupnom cetu
      $user_name = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">You</b>';
  }
  else
  //ako je poruku poslao neki drugi korisnik, prikazace njegov username pozivanjem funkcije get_user_name koju smo definisali u ovom fajlu
  {
   $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
  }


  //prikazujemo username, poruku i vreme slanja poruke
  $output .= '
   <li style="border-bottom:1px dotted #ccc">
   <p>'.$user_name.' - '.$row['chat_message'].' 
    <div align="right">
     - <small><em>'.$row['timestamp'].'</em></small>
    </div>
   </p>
  </li>
  ';
 }
 $output .= '</ul>';
 return $output;
}


?>