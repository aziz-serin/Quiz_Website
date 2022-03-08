<!DOCTYPE html>
  <html lang="en">
  <header>
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
            integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  	<title >Quiz Website/Home Page</title>
      <h1 style="text-align:center;">HOME PAGE</h1>
      
  </header>
  	<body>

    <style type="text/css">
        .topleftcorner{
            position:absolute;
            top:10px;
            left:10px;
        }
    </style>
  	<?php

  	session_start();
    if (!isset($_SESSION['user']))
      header("Location: login.php");

  	$un = $_SESSION['user'];
    $_SESSION['is_admin'] = false;


    if(!is_user_admin()){
      if(empty($_POST))output_privileges();
      else get_admin();
    }

    query_quiz();

    if(is_user_admin()){
      echo('<div class = "topleftcorner">
            <form method="GET" action="create_quiz.php">
              <input type="submit" name="create_quiz"
                class="btn btn-link" value="Create Quiz"/>
            </form>
            </div>');
    }

    // FUNCTIONS BELOW HERE
    //testing, testing
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

  function output_privileges(){
    echo '
    <div class = "topleftcorner">
    <button class="btn btn-link" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapseExample1 multiCollapseExample2">Get Admin</button>
    <form method="POST">
    <div class="row">
      <div class="col">
        <div class="collapse multi-collapse" id="multiCollapseExample1">
          <div class="card card-body">
            <div class = "form-group">
            <label for="admin_pwd"> Enter the password to earn admin privileges: </label>
            <input class = "form-control" type="password" id="admin_pwd" name="admin_pwd">
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="collapse multi-collapse" id="multiCollapseExample2">
        <div class="card card-body">
          <button class = "btn btn-primary" type = "submit"> Submit </button>
        </div>
      </div>
    </div>
    </div>
    </form>
    </div>
            ';
	}

	function get_admin(){
		$pw = $_POST['admin_pwd']; // admin password is database is 'admin'
		$un = $_SESSION['user'];
		$privilige = "admin";

		$pdo = connect_DB();
		$sql = "SELECT pwd FROM priviliges WHERE privilige = :privilige";

		$pdo = connect_DB();

    $stmt = $pdo->prepare($sql); 
    $stmt->execute([
    	'privilige' => $privilige
    ]);
    
    while($row = $stmt->fetch()){
    	if(password_verify($pw, $row['pwd'])){
    		
    		$sql = "UPDATE user_records SET is_admin = :is_admin WHERE user_name = :userName";

		    $stmt = $pdo->prepare($sql); 
		    $stmt->execute([
		    	'is_admin' => "1",
		    	'userName' => $un
		    ]);

    		header('Location: main_page.php'); // redirect to the home page
    		$pdo = null; // to close the connection with the database.
    	}
    	else{
    		echo("Invalid Credentials!");
            echo("<br>");
        $pdo = null; // to close the connection with the database.
    		return;
    	}
    }
	}

  function is_user_admin(){
    $un = $_SESSION['user'];

    $pdo = connect_DB();
    $sql = "SELECT is_admin FROM user_records WHERE user_name = :userName";
    $stmt = $pdo->prepare($sql); 
    $stmt->execute([
      'userName' => $un
    ]);
    while($row = $stmt->fetch()){
      if($row['is_admin'] == '1'){
        $_SESSION['is_admin'] = true;
        $pdo = null; // to close the connection with the database.
        return true;
        
      }
      else{
        $pdo = null; // to close the connection with the database.
        return false;
      }
    }
  }

  function query_quiz(){
    $pdo = connect_DB();
    $sql = "SELECT * FROM quiz";
    $stmt = $pdo->prepare($sql); 
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    if($row = $stmt->fetch()){
      $sql = "SELECT actual_name FROM user_records WHERE user_name = :userName";
      $stmt1 = $pdo->prepare($sql);
      $user = $row['author'];
      $stmt1->execute([
              'userName' => $user
      ]);
      $stmt1->setFetchMode(PDO::FETCH_ASSOC);
      $value = $stmt1->fetch();
      $actual_name = $value['actual_name'];
      $available = "Yes";
      if($row['quiz_available'] == "0"){
        $available = "No";
      }
      echo('
        <div style="text-align: center;">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Quiz Name</th>
              <th scope="col">Created By: </th>
              <th scope="col">Available?</th>
              <th scope="col">Recomended Duration</th>
              <th scope="col">View</th>
            </tr>
          </thead>
          <tbody>
      ');

      echo("
          <tr>
          <th scope='row'>-</th>
          <td>$row[quiz_name]</td>
          <td>$actual_name</td>
          <td>$available</td>
          <td>$row[quiz_duration]</td>
          <td>
        <form method='GET' action='quiz_info.php'>
          <input type='submit' name='$row[quiz_id]'
                  class='btn btn-link' value='View Quiz'/>
          <input type = 'hidden' name = 'quiz_id' value='$row[quiz_id]'>
          </form>
          </td>
          </tr>
        ");
      
      while($row = $stmt->fetch()){
        $sql = "SELECT actual_name FROM user_records WHERE user_name = :userName";
        $stmt1 = $pdo->prepare($sql);
        $user = $_SESSION['user'];
        $stmt1->execute([
          'userName' => $user
        ]);
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $value = $stmt1->fetch();
        $actual_name = $value['actual_name'];
        $available = "Yes";
        if($row['quiz_available'] == "0"){
          $available = "No";
        }
        echo("
          <tr>
          <th scope='row'>-</th>
          <td>$row[quiz_name]</td>
          <td>$actual_name</td>
          <td>$available</td>
          <td>$row[quiz_duration]</td>
          <td>
        <form method='GET' action='quiz_info.php'>
          <input type='submit' name='$row[quiz_id]'
                  class='btn btn-link' value='View Quiz'/>
          <input type = 'hidden' name = 'quiz_id' value='$row[quiz_id]'>
          </form>
          </td>
          </tr>
          ");
      }
      echo('</tbody>
            </table>
            </div>');
    }
    else{
      echo("No quiz found in the database");
    }
    
    $pdo = null;
  }
  	?>
    <style type="text/css">
        .topcorner{
            position:absolute;
            top:10px;
            right:10px;
        }
    </style>
    <div class = "topcorner">
    <form method="GET" action="logout.php">
        <input type="submit" name="log_out"
               class="btn btn-danger" value="Logout" />
    </form>
    </div>
    
    
  <!--      FOR BOOTSTRAP-->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  
</body>
  </html>