<!--
//index.php
!-->

<?php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id'])) //ako nije ulogovan korisni a pokusamo da pristiupimo index.php, vratice nas na login stranicu
{
 header("location:login.php");
}

?>

<html>  
    <head>  
        <title>MyChat - Home</title>  
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700">
    
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
        <link rel="stylesheet" href="css/index.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
    </head>  
    <body>  
       <div class="bgimg">
        <div class="container ">
        
   <br />
   <div class="one">
   <div class="form-header">
   <h3 align="center" class="myclass" style="color: #fff;">MyChat Application</a></h3><br />
   </div>
   
   <div class="table-responsive">
    <div class="tabela">
    <h4 align="center"  >Online Users</h4>
      <!-- GROUP CHAT -->
        <input type="hidden" id="is_active_group_chat_window" value="no" /> <!-- ako se otvori group chat menjamo na yes -->
        <button type="button" name="group_chat" id="group_chat" class="btn btn-warning btn-xs">Group Chat</button>

    <p align="right">Hi, <?php echo $_SESSION['username'];  ?>! <a class="btn btn-info" href="settings.php" role="button">Settings</a> <a class="btn btn-info" href="logout.php" role="button">Logout</a></p> </div>
    <div id="user_details"></div>
    <div id="user_model_details"></div>
   </div>
  </div>
  </div>
</div>
    </body>  
</html>  

<!-- GROUP CHAT PROZOR-->
<div id="group_chat_dialog" title="Group Chat Window" style="width:20vw; height:30wh;">

  <!-- polje gde se nalaze poruke -->
 <div id="group_chat_history" style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;">

 </div>
 <div class="form-group">
    <textarea name="group_chat_message" id="group_chat_message" class="form-control"></textarea> <!-- polje za kucanje poruke -->
 </div>
 <div class="form-group" align="right">
  <button type="button" name="send_group_chat" id="send_group_chat" class="btn btn-info">Send</button>
 </div>
</div>



<script>  
$(document).ready(function(){

 fetch_user(); //kada se stranica ocitava u browser pozvace se fetch_user funkcija koja prikazuje user_details

 setInterval(function(){  //poziva se na svaku sekundu
  update_last_activity(); //na 5sec ce last_activity da se apdejtuje u login tabeli
  fetch_user(); //na svakih 5 sekundi ce da rifresuje user data na stranici
  update_chat_history_data();
  fetch_group_chat_history(); 
 }, 1000); 

 function fetch_user()
 {
  $.ajax({
   url:"fetch_user.php", //ovde saljemo request
   method:"POST",        //koristimo post metod za slanje podataka serveru
   success:function(data){ //ako je zahtev uspesno prihvacen, prikazace user_details preko #user_details id

    $('#user_details').html(data);
   }
  })
 }

 function update_last_activity() //za ostatus(online/ofline)
 {
  $.ajax({
   url:"update_last_activity.php", //ovde saljemo zahtev
   success:function() //poziva se ako je uspesno obradjen request
   {

   }
  })
 }

 function make_chat_dialog_box(to_user_id, to_user_name)
 {
  var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
  //ima poseban id za svaki chat prozor

  modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">'; //prozor u kome su poruke

  modal_content += fetch_user_chat_history(to_user_id); //kada otvorimo prozor prikazuje poruke
  modal_content += '</div>';
  modal_content += '<div class="form-group">';
  modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>'; //generisano dynamic name and id value for each user chat modal dialog box, textarea prostor za kucanje poruke OVDE MOZEMO DA PISEMO PORUKU

  modal_content += '</div><div class="form-group" align="right">';
  modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>'; //button za slanje poruka, generisali smo dynamic id value u to_user_id var

  $('#user_model_details').html(modal_content); //html kod iz modal_contant se dodaje u #user_model_details
 }

 $(document).on('click', '.start_chat', function(){ //kada kliknemo na dugme klase start chat dugme otvaramo prozor za cetovanje: 
  var to_user_id = $(this).data('touserid');
  var to_user_name = $(this).data('tousername');
  make_chat_dialog_box(to_user_id, to_user_name); //OVDE POZIVAMO FUNKCIJU make_chat_dialog_box i pravimo dynamic chat box sa odredjenim korisnikom  
  $("#user_dialog_"+to_user_id).dialog({ //inicijalizujemo jquery dialog box plugin na stranici
   autoOpen:false, //onemogucava da se automatski prikaze cet(zelimo da ga prikazemo klikom na START CHAT)
   width:400
  });
  $('#user_dialog_'+to_user_id).dialog('open'); //OTVARA CHAT BOX KADA KLIKNEMO NA START CHAT DUGME
  $('#chat_message_'+to_user_id).emojioneArea({
   pickerPosition:"top",
   toneStyle: "bullet"
  });
 });

//SLANJE PORUKE:
 $(document).on('click', '.send_chat', function(){
  var to_user_id = $(this).attr('id'); //vraca id
  var chat_message = $('#chat_message_'+to_user_id).val(); //cuva u chat_message
  $.ajax({
   url:"insert_chat.php", //ovde saljemo request
   method:"POST",
   data:{to_user_id:to_user_id, chat_message:chat_message},
   success:function(data)
   {
    //$('#chat_message_'+to_user_id).val(''); 
    //clear text polje za slanje kada se posalje
    var element = $('#chat_message_'+to_user_id).emojioneArea();
    element[0].emojioneArea.setText('');
    $('#chat_history_'+to_user_id).html(data);
   }
  })
 });

 //fetch-uje istoriju dopisicanja sa odredjenim korisnikom
 function fetch_user_chat_history(to_user_id) 
 {
  $.ajax({
   url:"fetch_user_chat_history.php", // ovde saljemo zahtev
   method:"POST",                     //za slanje podataka serveru
   data:{to_user_id:to_user_id},      //to_user_id saljemo serveru
   success:function(data){            //ako je prihvacen zahtev dobice chat history data sa servera
    $('#chat_history_'+to_user_id).html(data); 
   }
  })
 }

 //apdejtuje otvoreni chat prozor na svakih nekoliko sekundi(pozvali smo gore preko setInterval), za realtime 
 function update_chat_history_data()
 {
  $('.chat_history').each(function(){ //pristupamo svim html poljima sa klasom chat_history
   var to_user_id = $(this).data('touserid');
   fetch_user_chat_history(to_user_id);
  });
 }

 $(document).on('click', '.ui-button-icon', function(){
  $('.user_dialog').dialog('destroy').remove();
 });

 $(document).on('focus', '.chat_message', function(){
  var is_type = 'yes';
  $.ajax({
   url:"update_is_type_status.php",
   method:"POST",
   data:{is_type:is_type},
   success:function()
   {

   }
  })
 });

 $(document).on('blur', '.chat_message', function(){
  var is_type = 'no';
  $.ajax({
   url:"update_is_type_status.php",
   method:"POST",
   data:{is_type:is_type},
   success:function()
   {
    
   }
  })
 });

  $('#group_chat_dialog').dialog({
      autoOpen:false, //ne otvara se prozor dok ne kliknemo na groupchat
      width:400
  });

  $('#group_chat').click(function(){
    //kada kliknemo na group chat(id je group_chat) dugme otvara se prozor za grupni cet i menja se vrednost na yes (znaci da je grupni cet otvoren)
      $('#group_chat_dialog').dialog('open');
      $('#is_active_group_chat_window').val('yes');
      $('#group_chat_message').emojioneArea({
   pickerPosition:"top",
   toneStyle: "bullet"
  });
       fetch_group_chat_history(); //kada kliknemo na group chat poziva se ova funkcija i prikazuju nam se poruke
  });

  $('#send_group_chat').click(function(){
     var chat_message = $('#group_chat_message').val(); //poruka koja je ukucana u polju za slanje
      var action = 'insert_data';
      if(chat_message != '')
     {
        $.ajax({
        url:"group_chat.php", //saljemo zahtev na group_chat.php
        method:"POST",
        data:{chat_message:chat_message, action:action},
        success:function(data){
          var element = $('#group_chat_message').emojioneArea();
      element[0].emojioneArea.setText('');
         $('#group_chat_message').val('');  //cisti polje za kucanje poruke
         $('#group_chat_history').html(data); //prikazuje poruke u grupnom cetu
      }
  })
 }
});

  //za prikazivanje najnovije poruke u grupnom cetu(real-time)
  function fetch_group_chat_history()
  {

    //akko je group chat prozor otvoren, azuriraj poruke
    var group_chat_dialog_active = $('#is_active_group_chat_window').val();
    var action = "fetch_data";
    if(group_chat_dialog_active == 'yes') //ako je otvoren group chat box
    {
      $.ajax({
        url:"group_chat.php",     //saljemo zahtev na group_chat.php
        method:"POST",
        data:{action:action},
        success:function(data)
      {
        //azurira poruke u otvorenom group chat prozoru ako je zahtve prihvacen
      $('#group_chat_history').html(data);
   }
  })
 }
}

 
$(document).on('click', '.remove_chat', function(){
  var chat_message_id = $(this).attr('id');
  if(confirm("Are you sure you want to remove this message?"))
  {
   $.ajax({
    url:"remove_chat.php",
    method:"POST",
    data:{chat_message_id:chat_message_id},
    success:function(data)
    {
     update_chat_history_data();
    }
   })
  }
 });
 
});  
</script>