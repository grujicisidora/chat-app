<?php

//fetch_user_chat_history.php

include('database_connection.php');

session_start();

//ova funkcija koja se poziva je definisana u database_connection.php i vraca chat history sa odredjenim korisnikom sa kojim je otvoren chat box
echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $connect); //to_user_id je poslat kao data preko ajax zahteva u fetch_user_chat_history(to_user_id) funkciji u index.php

?>