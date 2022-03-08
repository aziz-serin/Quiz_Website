<!DOCTYPE html>
  <html lang="en">
    <head>
      <title>Quiz Website/Create</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        
      <h1>CREATE QUIZ</h1>
    </head>

    <body style="text-align:center;">
<?php
	  	session_start();
	    if (!isset($_SESSION['user']))
	      header("Location: login.php");
	    if(!$_SESSION['is_admin'])
	    	header("Location: main_page.php");
          $quiz_id = get_largest_quiz_id() + 1;
          $_SESSION['quiz_id'] = $quiz_id;
    
          if(empty($_POST)){
              get_quiz_info();
          }
          else{
            $_SESSION['time'] = $_POST['time'];
            $time = preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $_SESSION['time']);
  
            if(!$time){
              header("Location:create_quiz.php");
              return;
            }
            $_SESSION['number_of_questions'] = $_POST['quantity'];
            $_SESSION['quizname'] = $_POST['quizname'];
            $_SESSION['quiz_available'] = $_POST['available'];
            header("Location:create_qa.php");
            //validate();
            //question_input($_SESSION['number_of_questions']);
          }

          
    
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
    
          function get_largest_quiz_id(){
            $pdo = connect_DB();
            $sql = "SELECT MAX(quiz_id) FROM quiz";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if($row = $stmt->fetch()){
              $pdo = null;
              return $row['MAX(quiz_id)'];
            }
            else{
              $pdo = null;
              return 0;
            }
          }
    
          function get_quiz_info(){
            echo("
              <form method='POST'>
              <div class='form-group'>
                  <label for='quizname'>Quiz Name</label>
                  <br>
                  <small id='quizname_help' class='form-text text-muted'>(max length 50)</small>
                  <br>
                  <input type='text' class = 'form-control' required='required' id='quizname' name='quizname' maxlength='50'>
                  <br>
              </div>
              <div class='form-group'>
                <label for='quantity'>How many questions would you like to create?</label>
                <br>
                <small id='question_help' class='form-text text-muted'>(Between 1 and 50)</small>
                <br>
                <input type='number' class = 'form-control' required='required' id='quantity' name='quantity' min='1' max'50'>
                <br>
              </div>
              <div class='form-group'>
                <label for='time'>Enter the duration</label>
                <small id='time_help' class='form-text text-muted'>(in the format hh:mm:ss)</small>
                <br>
                <input type='text' class = 'form-control' required='required' id='time' name='time' maxlength='8'>
                <br>
              </div>
              <input class='radio' type='radio' name='available' value='1' checked> <span>Available</span>
              <input class='radio' type='radio' name='available' value='0'> <span>
                Not Available</span>
              <br>
              <input type='submit' class = 'btn btn-link' value='Create'>
              </form>
              ");
          }
?>

        <!--      FOR BOOTSTRAP-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


    </body>
    </html>