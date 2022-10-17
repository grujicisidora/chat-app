<!--
//login.php
!-->

<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id'])) //ako je korinik vec ulogovan i hoce da pristupi login stranici, prebacujemo ga na index starnicu
{
 header('location:index.php');
}

if(isset($_POST["login"]))
{
 $query = "
   SELECT * FROM login 
    WHERE username = :username
 ";
 $statement = $connect->prepare($query);
 $statement->execute(
    array(
      ':username' => $_POST["username"]
     )
  );
  $count = $statement->rowCount();
  if($count > 0)
 { //ako je pronadjen korisnik sa unetim username-om
  $result = $statement->fetchAll();
    foreach($result as $row)
    {
      if(password_verify($_POST["password"], $row["password"]))
      {
        //ako je tacan password isti kao hash password koji je zapamcen u
        //login tabeli 

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        /* u user_id koloni login_details tabele uneti $row['user_id'] */
        $sub_query = "
        INSERT INTO login_details
        (user_id) 
        VALUES ('".$row['user_id']."')
        ";
        $statement = $connect->prepare($sub_query);
        $statement->execute(); // insertuje u login_details tabelu
        $_SESSION['login_details_id'] = $connect->lastInsertId(); /* 
        vraca lastInsertId zapampcen u $_SESSION['login_details_id']
        */

        header("location:index.php");
      }
      else
      {
       $message = "<label>Wrong Password</label>";
      }
    }
 }
 else
 {
  $message = "<label>Wrong Username</labe>";
 }
}

?>

<html>  
    <head>  
        <title>MyChat - Login</title>  
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700">      
  <link rel="stylesheet" type="text/css" href="css/signin.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>  
    <body>  
        <div class="signin-form">
    <form action="" method="post">
      <div class="form-header panel-heading">
        <h2>Sign In</h2>
        <p>Login to MyChat</p>
      </div>
  
      <p class="text-danger"><?php echo $message; ?></p> <!-- prikazuje gresku ako postoji -->

      <div class="form-group">
          <label>Enter Username</label>
          <input type="text" name="username" class="form-control" required />
      </div>
      <div class="form-group">
          <label>Enter Password</label>
          <input type="password" name="password" class="form-control" required />
      </div>
      <div class="form-group">
          <input type="submit" name="login" class="btn btn-primary btn-block btn-lg" value="Login" />
      </div>

    </form> 

	  <div class="text-center " style="color: #D01727;">Don't have an account? <a href="register.php">Create new account</a></div>

    </div>
   
    </body>  
</html>
