<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_POST["update_username"]))
{
  $query = "
   SELECT * FROM login 
    WHERE user_id = :user_id
 ";
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
      ':user_id' => $_SESSION["user_id"]
     )
 );
 
 $result = $statement->fetchAll();
 foreach($result as $row){
  
  if (empty($_POST['username']) or $_POST['username']==$row["username"]){
      $message = 'Choose other username!';
  }

  else
  {
        $new_username = $_POST['username'];
        $query = "
        UPDATE login 
        SET username = '".$new_username."' 
        WHERE user_id = '".$_SESSION["user_id"]."'
        ";

        $statement = $connect->prepare($query);

        $statement->execute();
       
        session_destroy();
        header("location: index.php");
     
  }
}
  
}

if(isset($_POST['update_password'])){
  $query = "
   SELECT * FROM login 
    WHERE user_id = :user_id
 ";

 $statement = $connect->prepare($query);
 $statement->execute(
  array(
      ':user_id' => $_SESSION["user_id"]
     )
 );
 
 $result = $statement->fetchAll();
 foreach($result as $row){
  
  if(password_verify($_POST["password"], $row["password"]))
  {
    if (!empty($_POST['new_password'])){
      if($_POST['new_password'] == $_POST['confirm_password']){
        $new_hashed_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $query = "
        UPDATE login 
        SET password = '".$new_hashed_pass."' 
        WHERE user_id = '".$_SESSION["user_id"]."'
        ";

        $statement = $connect->prepare($query);

        $statement->execute();
       
        session_destroy();
        header("location: index.php");
      }
      else{
            $message = 'Your new password does not match your confirmed password!';
      }
  }
  else{
    $message = 'You have to choose a new password!';
  }
}
  else{
    $message = 'Wrong current password!';
  }
}
}
?>

<html>  
    <head>  
        <title>MyChat - Settings</title>  
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/signin.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700">
    </head>  
    <body>  
        <div class="signin-form">
    <form action="" method="post">
      <div class="form-header">
        <h2>Settings</h2>
        <p>Change your username or password</p>
      </div>
  
      <p class="text-danger"><?php echo $message; ?></p>

      <div class="form-group">
          <label>Enter New Username</label>
          <input type="text" id="clear" name="username" class="form-control" />
      </div>
     
      <div class="form-group">
          <input type="submit" name="update_username" class="btn btn-primary btn-block btn-lg" value="Update" />
      </div>
      <div class="form-group">
          <label>Enter Your Current Password</label>
          <input type="password" name="password" class="form-control" />
      </div>
      <div class="form-group">
          <label>Enter New Password</label>
          <input type="password" name="new_password" class="form-control" />
      </div>
      <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" />
      </div>
      <div class="form-group">
          <input type="submit" name="update_password" class="btn btn-primary btn-block btn-lg" value="Update" />
      </div>

    </form> 

	  <div class="text-center small"><a href="index.php" style="color: #fff; font-size: 15px;">Back to MyChat -></a></div>

    </div>
   
    </body>  
</html>