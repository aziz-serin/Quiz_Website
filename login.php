<!DOCTYPE html>
  <html lang="en">
    <head>
      <title>Quiz Website/Login</title>
      <h1 style="text-align:center;">LOGIN</h1>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>

    <body>

    	<?php
			function loginForm() {
			    echo '
			      <form method="POST">
			      <div class = "form-group">
				      <label for="username">Username</label>
				      <br>
				      <input type="text" class="form-control" id="username" name="username">
				      <br>
                  </div>
                  <div class = "form-group">
				      <label for="password">Password</label> 
				      <br>
				      <input type="password" class="form-control" id="password" name="password">
				      <br>
				  </div>
				  <div style="text-align:center;">
				      <input type="submit" class="btn btn-primary" value="Login">
				      <br>
                  </div>
				  </form>
			      <form method="GET" style="text-align:center;" action="register.php">
				      <label> Want to register? </label>
				      <br>
				      <button class="btn btn-secondary" type = "submit">Register</button>
			      </form> 
			    '; } 
		
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

        function login(){
        	$un = $_POST['username'];
		      $pw = $_POST['password'];

		      $sql = "SELECT user_name, password_ FROM user_records WHERE user_name = :userName";

		      $pdo = connect_DB();
		      $stmt = $pdo->prepare($sql); 
		      $stmt->execute([
		      	'userName' => $un
		      ]);

		      $stmt->setFetchMode(PDO::FETCH_ASSOC);

		      while($row = $stmt->fetch()){
		      	if(password_verify($pw, $row['password_'])){
		      		session_start();
		      		$_SESSION['user'] = $row['user_name'];
		      		header('Location: main_page.php'); // redirect to the home page
		      		$pdo = null; // to close the connection with the database.
		      	}
		      	else{
		      		echo("Invalid Credentials!");
		      		return;
		      	}
		      }
		      
        }

        if(empty($_POST)) loginForm();
        else login();
    	?>

        <!--      FOR BOOTSTRAP-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    </body>