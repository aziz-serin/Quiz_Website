<!DOCTYPE html>
<html lang="en">
<head>
  <title>Quiz Website/Quiz</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  
  
  <h1>QUIZ</h1>
</head>

<body style="text-align:center;">

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
  if (!isset($_GET['quiz_id']))
    header("Location: main_page.php");
  
  $var = $_GET['quiz_id'];
  
  query_quiz($var);
  show_attempts($var);
  
  function connect_DB() {
    $servername = "localhost";
    $username = "testing";
    $password = "testing";
    
    try {
      $conn = new PDO("mysql:host=$servername;dbname=quiz_app", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $conn;
    }
    catch(PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }
  
  function query_quiz($quiz_id)
  {
    $pdo = connect_DB();
    $sql = "SELECT * FROM quiz WHERE quiz_id = $quiz_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
  
    if ($row = $stmt->fetch()) {
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

      $tab = "\t";
      echo("<div class='container'>
            <div class = 'row'>
            <div class='col-sm'>
            Quiz:  $row[quiz_name] $tab
            </div>
            <div class='col-sm'>
            Created By:  $actual_name $tab
            </div>
            <div class='col-sm'>
            Available: $available $tab
            </div>
            <div class='col-sm'>
            Duration: $row[quiz_duration]
            </div>
            </div>
            </div>
      ");
      if($row['quiz_available'] == "1"){
          echo("
            <form method='GET' action='take_quiz.php'>
              <input type='submit' name='$row[quiz_id]'
                      class='btn btn-link' value='Take Quiz'/>
              <input type = 'hidden' name = 'quiz_id' value='$row[quiz_id]'>
              </form>
            ");
      }
      if ($_SESSION['is_admin']) {
        echo("
        <div class='topleftcorner'>
        <form method='GET' action='edit_quiz.php'>
          <input type='submit' name='$row[quiz_id]'
                  class='btn btn-primary' value='Edit Quiz'/>
          <input type = 'hidden' name = 'quiz_id' value='$row[quiz_id]'>
          </form>
          </div>
        ");
      }
    }
    $pdo = null;
  }
  function show_attempts($quiz_id){
      $pdo = connect_DB();
      $sql = "SELECT question FROM question WHERE quiz_id = :quizID";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['quizID' => $quiz_id]);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $available_score = 0;
      while($row = $stmt->fetch()){
          $available_score++;
      }
      $sql = "SELECT date_of_attempt, score FROM attempt WHERE quiz_id = :quiz_id AND user_name = :username";
      $stmt = $pdo->prepare($sql);
      $user_name = $_SESSION['user'];
      $stmt->execute([
          'quiz_id' => $quiz_id,
          'username' => $user_name
      ]);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      echo("<br>");
      echo("Total Achievable Score: $available_score ");
      echo("<br>");
      $is_there_an_attempt = 0;
      if($row = $stmt->fetch()){
          $is_there_an_attempt = 1;
          $date = $row['date_of_attempt'];
          $score = $row['score'];
          //echo("Attempt Date: $date | Score: $score");
          //echo("<br>");
          echo("
            <table class = 'table'>
            <thead class='thead-dark'>
            <tr>
              <th scope='col'>#</th>
              <th scope='col'>Attempted</th>
              <th scope='col'>Score</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <th scope='row'>-</th>
              <td>$date</td>
              <td>$score</td>
            </tr>
            ");
          while($row = $stmt->fetch()){
            $date = $row['date_of_attempt'];
            $score = $row['score'];
            echo("
            <tr>
              <th scope='row'>-</th>
              <td>$date</td>
              <td>$score</td>
            </tr>
            ");
          }
        echo('</tbody>
            </table>
            ');
      }
      else{
          echo("No previous attemps found!");
      }
      // WHERE STORED PROCEDURE IS CALLED
      if(!$_SESSION['is_admin']){ // display these messages if the user is not an admin
        $stmt = $pdo->prepare($sql);
        $user_name = $_SESSION['user'];
        $stmt = $pdo->prepare("CALL display_low_score(:userName, :quizID, @is_true)");
        $stmt->bindParam(':userName', $user_name);
        $stmt->bindParam('quizID', $quiz_id);
  
        $stmt->execute();
  
        $outputArray = $pdo->query("SELECT @is_true")->fetch(PDO::FETCH_ASSOC);
        $is_true = $outputArray['@is_true'];
        if($is_there_an_attempt){
          if($is_true == "1"){
            echo("<br>");
            echo("Before you take the quiz again you may want to revise.");
            echo("<br>");
          }
          else if($is_true == "0"){
            echo("<br>");
            echo("Good Job! You can proceed to the next quiz.");
            echo("<br>");
          }
        }
        
      }
      $pdo =null;
  }
?>


<!--      FOR BOOTSTRAP-->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>



</body>
</html>



