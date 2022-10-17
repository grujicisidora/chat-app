<?php
include('database_connection.php');

session_start();

//prilikom slanja zahteva (klikom na SEND u grupnom cetu) na ovu stranicu poslali smo chat_message i action
//ako je kliknuto da se posalje poruka u grupni cet
if($_POST["action"] == "insert_data")
{
 $data = array(
  ':from_user_id'  => $_SESSION["user_id"],		//id onoga ko salje poruku
  ':chat_message'  => $_POST['chat_message'],	//sadrzaj poruke
  ':status'   => '1'	//status ide prvo na unseen
 );

//u to_user_id kolonu chat_message grupe ne stavljamo nista, pa ce da bude uvek tu nula za grupne poruke
 $query = "
 INSERT INTO chat_message 
 (to_user_id,from_user_id, chat_message, status) 
 VALUES ('0', :from_user_id, :chat_message, :status)
 ";

 $statement = $connect->prepare($query);

 if($statement->execute($data))
 {
  echo fetch_group_chat_history($connect);
 }

}

if($_POST["action"] == "fetch_data")
{
 echo fetch_group_chat_history($connect);
}

?>
