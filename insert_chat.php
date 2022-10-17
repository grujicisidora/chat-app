
<?php

//insert_chat.php

include('database_connection.php');

session_start();

$data = array(
 ':to_user_id'  => $_POST['to_user_id'], //user_id kome saljemo poruku
 ':from_user_id'  => $_SESSION['user_id'], //user_id kor. kojji salje poruku 
 ':chat_message'  => $_POST['chat_message'],
 ':status'   => '1' //kada se posalje poruka ona je prvo na unseen
);
// azurira elemente u chat_message tabeli:

$query = "
INSERT INTO chat_message 
(to_user_id, from_user_id, chat_message, status) 
VALUES (:to_user_id, :from_user_id, :chat_message, :status)
";

$statement = $connect->prepare($query);

if($statement->execute($data)) //nakon uspesnog slanja poruke zelimo da prikazemo sve poruke sa odredjenim korisnikom
{

 echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $connect);
}

?>
