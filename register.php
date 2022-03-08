<!DOCTYPE html>
  <html lang="en">
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
      <title>Quiz Website/Register</title>


      <h1 style="text-align:center;">  REGISTER</h1>
    </head>

    <body>


      
      <?php

        function connect_DB() {
          $servername = "localhost";
          $username = "testing";
          $password = "testing";

          try {
            $conn = new PDO("mysql:host=$servername;dbname=quiz_app", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn; 
          } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
          }
        }

        if(empty($_POST)) registerForm();
        else register();
        
    ?>


    <?php

    function registerForm() {
    echo '
     
      <form method="POST">
        <div class = "form-group">
          <label for="username">Username</label>
          <br>
          <input type="text" class="form-control" required="required" id="username" name="username" minlength="5" maxlength="50">
          <br>
        </div>
        <div class = "form-group">
          <label for="username">Name</label>
          <br>
          <input type="text" class = "form-control" required="required" id="name" name="name" minlength="5" maxlength="100">
          <br>
         </div>
       <div class = "form-group">
          <label for="password">Password</label>
          <br>
          <input  class = "form-control" required="required" type="password" id="password" minlength="5" name="password">
          <br>
       </div>
       <div class = "form-group">
          <label for="password"> Confirm Password</label>
          <br>
          <input class = "form-control" type="password" id="confirm_password" minlength="5" name="confirm_password">
          <br>
      </div>
      <div style="text-align:center;" >
      <input  class="btn btn-primary" type="submit" value="Register">
      <br>
      </div>
      
      </form>
      <form style="text-align:center;" method="GET" action="login.php">
      <label> Do you have an account?</label>
      <br>
      <button class="btn btn-secondary" type = "submit">Login</button>
      </form>
    '; }

    function register(){
      $un = $_POST['username'];
      $pw = $_POST['password'];
      $name = $_POST['name'];
      $cpw = $_POST['confirm_password'];
      $admin_ = "0";

      if($pw != $cpw){
        echo("Passwords do not match!");
        return;
      }
      else{
        $pw = password_hash($pw, PASSWORD_DEFAULT);

        $pdo = connect_DB();

        $sql = "SELECT user_name FROM user_records WHERE user_name = :userName";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'userName' => $un
        ]);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if($row = $stmt->fetch()){
          echo("User already exists!");
          $pdo = null; // to close the connection with the database.
          return;
          }        

        $sql = "INSERT INTO user_records(user_name, actual_name, password_, is_admin)
                VALUES(:username, :actualName, :password, :admin)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'username' => $un,
          'actualName' => $name,
          'password' => $pw,
          'admin' => $admin_

        ]);
        session_start();
        $_SESSION['user'] = $un;
        $pdo = null; // to close the connection with the database.
        header('Location: main_page.php'); // redirect to the home page
        echo("Inserted the records!"); 
      }
    }
    ?>
<!--      FOR BOOTSTRAP-->
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
    </html>