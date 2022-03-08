<!DOCTYPE html>
<html lang="en">
<head>
  <title>Quiz Website/Create Q&A</title>
  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  
  <h1>CREATE QUIZ</h1>
</head>

<body style="text-align:center;">


<?php
  session_start();
  if (!isset($_SESSION['user']))
    header("Location: login.php");
  if (!isset($_SESSION['quiz_id']))
    header("Location: main_page.php");
  if(!$_SESSION['is_admin'])
    header("Location: main_page.php");
  
  $quiz_id = $_SESSION['quiz_id'] ;
  
  if(!empty($_POST)){
    upload_quiz($_SESSION['number_of_questions']);
    unset($_SESSION['number_of_questions']);
    unset($_SESSION['quizname']);
    unset($_SESSION['quiz_available']);
    unset($_SESSION["quiz_id"]);
    header("Location:main_page.php");
  }
  else{
    question_input($_SESSION['number_of_questions']);
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
  
  function question_input($question_amount)
  {
    $pdo = connect_DB();
    $quiz_id = $_SESSION['quiz_id'];
    $sql = "SELECT MAX(question_id) FROM question";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row = $stmt->fetch();
    $question_id = $row['MAX(question_id)'] + 1;
    $stop = $question_id + $question_amount;
  
    echo("<form method='POST'>");
    $counter = 1;
    while ($question_id < $stop) {
      echo("Question " . $counter. "<br>");
      $id = "q" . strval($question_id);
      $ans_id_1 = "a" . strval($question_id) . "1";
      $ans_id_2 = "a" . strval($question_id) . "2";
      $ans_id_3 = "a" . strval($question_id) . "3";
      $ans_id_4 = "a" . strval($question_id) . "4";
      $ans_id_1_tf = "a" . strval($question_id) . "1" . "tf";
      $ans_id_2_tf = "a" . strval($question_id) . "2" . "tf";
      $ans_id_3_tf = "a" . strval($question_id) . "3" . "tf";
      $ans_id_4_tf = "a" . strval($question_id) . "4" . "tf";
      $question_answer = "a" . strval($question_id);
      echo("
                <textarea id='$id' name='$id' required='required' rows='5' cols='60'>Question Here</textarea>
                <br>
                <input type='radio' name = '$question_answer' value='$ans_id_1_tf' checked>
                <textarea id='$ans_id_1' name='$ans_id_1' required='required' rows='3' cols='50'>Answer 1</textarea>
                <br>
                <input type='radio' name = '$question_answer' value='$ans_id_2_tf'>
                <textarea id='$ans_id_2' name='$ans_id_2' required='required' rows='3' cols='50'>Answer 2</textarea>
                <br>
                <input type='radio' name = '$question_answer' value='$ans_id_3_tf'>
                <textarea id='$ans_id_3' name='$ans_id_3' required='required' rows='3' cols='50'>Answer 3</textarea>
                <br>
                <input type='radio' name = '$question_answer' value='$ans_id_4_tf'>
                <textarea id='$ans_id_4' name='$ans_id_4' required='required' rows='3' cols='50'>Answer 4</textarea>
                <br>
                ");
      $question_id++;
      $counter++;
    }
    echo('<input type = "submit" class = "btn btn-primary" value = "CREATE QUIZ">
                </form>');
  }
  function upload_quiz($question_amount){
    $pdo = connect_DB();
    
    $quiz_id = $_SESSION['quiz_id'];
    
    $username = $_SESSION['user'];
    
    $sql = "SELECT MAX(question_id) FROM question";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row = $stmt->fetch();
    $question_id = $row['MAX(question_id)'] + 1;
    $stop = $question_id + $question_amount;
    
    
    $sql = "INSERT INTO quiz(quiz_id, quiz_name, author, quiz_available, quiz_duration)
            VALUES(:quiz_id, :quiz_name, :quiz_author, :quiz_available, :quiz_duration)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      'quiz_id' => $_SESSION['quiz_id'],
      'quiz_name' => $_SESSION['quizname'],
      'quiz_author' => $username,
      'quiz_available' => $_SESSION['quiz_available'],
      'quiz_duration' => $_SESSION['time']
    ]);
    
    while($question_id < $stop){
      $id = "q" . strval($question_id);
      $ans_id_1 = "a" . strval($question_id) . "1";
      $ans_id_2 = "a" . strval($question_id) . "2";
      $ans_id_3 = "a" . strval($question_id) . "3";
      $ans_id_4 = "a" . strval($question_id) . "4";
      $ans_id_1_tf = "a" . strval($question_id) . "1". "tf";
      $ans_id_2_tf = "a" . strval($question_id) . "2". "tf";
      $ans_id_3_tf = "a" . strval($question_id) . "3". "tf";
      $ans_id_4_tf = "a" . strval($question_id) . "4". "tf";
      $question_answer = "a" . strval($question_id);
      
      
      $q = $_POST[$id];
      
      $sql = "INSERT INTO question(quiz_id, question_id, question) VALUES(:quiz_id, :question_id, :question)";
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'quiz_id' => $_SESSION['quiz_id'],
        'question_id' => $question_id,
        'question' => $q
      ]);
      
      $sql = "INSERT INTO answers(question_id, answer_id, answer, is_true) VALUES(:question_id, :answer_id, :answer, :is_true)";
      
      $is_true = "0";
      if($_POST[$question_answer] == $ans_id_1_tf){
        $is_true = "1";
      }
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'question_id' => $question_id,
        'answer_id' => 1,
        'answer' => $_POST[$ans_id_1],
        'is_true' => $is_true
      ]);
      
      $is_true = "0";
      if($_POST[$question_answer] == $ans_id_2_tf){
        $is_true = "1";
      }
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'question_id' => $question_id,
        'answer_id' => 2,
        'answer' => $_POST[$ans_id_2],
        'is_true' => $is_true
      ]);
      
      $is_true = "0";
      if($_POST[$question_answer] == $ans_id_3_tf){
        $is_true = "1";
      }
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'question_id' => $question_id,
        'answer_id' => 3,
        'answer' => $_POST[$ans_id_3],
        'is_true' => $is_true
      ]);
      
      $is_true = "0";
      if($_POST[$question_answer] == $ans_id_4_tf){
        $is_true = "1";
      }
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'question_id' => $question_id,
        'answer_id' => 4,
        'answer' => $_POST[$ans_id_4],
        'is_true' => $is_true
      ]);
      
      $question_id++;
    }
    
    $pdo = null;
  }
  
  
  
?>

<!--      FOR BOOTSTRAP-->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


</body>
</html>