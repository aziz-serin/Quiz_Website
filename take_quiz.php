<!DOCTYPE html>
  <html lang="en">
    <head>
      <title>Quiz Website/TAKE</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

      <h1 style="text-align:center;">TAKE QUIZ</h1>
    </head>

    <body>

    <style type="text/css">
        .shift_left{
            position:relative;
            left:50px;
        }
        .shift_left_less{
            position:relative;
            left:35px;
        }
    </style>
    
    <?php
    session_start();
    if (!isset($_SESSION['user']))
      header("Location: login.php");
    if (!isset($_GET['quiz_id']))
      header("Location: main_page.php");

      $var = $_GET['quiz_id'];

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
      
      if(empty($_POST)){
          display_questions($var);
      }
      else{
          record_attempt($var);
      }
      
      function display_questions($quiz_id){
        $pdo = connect_DB();
        // Fetch Quiz
        $sql = "SELECT quiz_name, quiz_duration  FROM quiz WHERE quiz_id = :quizID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
                'quizID' => $quiz_id
        ]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $quiz_name = $row['quiz_name'];
        $quiz_duration = $row['quiz_duration'];
        // Fetch Question
        $sql = "SELECT question, question_id FROM question WHERE quiz_id = :quiz_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'quiz_id' => $quiz_id
        ]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        echo("  <form method='POST'><br>
                <div style='text-align:center;'>
                <p>Quiz: $quiz_name</p>
                <p>Advised time for this quiz: $quiz_duration</p>
                </div>
                ");
        $qn  = 1;
        while($row = $stmt->fetch()){
          $question_id = $row['question_id'];
          $question_answer = "a" . strval($question_id);
          $question_text = $row['question'];
          echo("
            <div class='shift_left_less'>
            <p>$qn. $question_text</p>
            </div>
          ");
          $sql = "SELECT answer, is_true FROM answers WHERE question_id = :question_id";
          $stmt1 = $pdo->prepare($sql);
          $stmt1->execute([
            'question_id' => $question_id
          ]);
          $stmt1->setFetchMode(PDO::FETCH_ASSOC);
          $i = 1;
          while($values = $stmt1->fetch()) {
            $ans_id = "a" . strval($question_id) . strval($i);
            $ans_id_tf = "a" . strval($question_id) . strval($i) . "tf";
            $answer_text = $values['answer'];
            echo("
                <div class='shift_left'>
                <input type='radio' name ='$question_answer' value='$ans_id_tf'>
                <label>$answer_text</label>
                <br>
                </div>");
            $i++;
          }
          $qn++;
        }
        echo('
        <div style="text-align:center;">
        <button type = "submit" class = "btn btn-primary" name = "action"> Submit Answers </button>
        </div>
        </form>
        ');
        $pdo = null;
      }
      function record_attempt($quiz_id){
          $pdo = connect_DB();
          $sql = "SELECT question_id FROM question WHERE quiz_id = :quiz_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([
            'quiz_id' => $quiz_id
          ]);
          // Score of the quiz
          $score = 0;
          while($row = $stmt->fetch()){
              $question_id = $row['question_id'];
              $question_answer = "a" . strval($question_id);
              $sql = "SELECT answer_id FROM answers WHERE question_id = :questionID AND is_true = :is_true";
              $stmt1 = $pdo->prepare($sql);
              $is_true = "1";
              $stmt1->execute([
                'questionID' => $question_id,
                'is_true' => $is_true
              ]);
              while($value = $stmt1->fetch()){
                $ans_id_tf = "a" . strval($question_id) . strval($value['answer_id']) . "tf";
                if($_POST[$question_answer] == $ans_id_tf){
                    $score++;
                }
              }
          }
          $date = date("Y-m-d H:i:s");
          $username = $_SESSION['user'];
          $sql = "INSERT INTO attempt(quiz_id, user_name, date_of_attempt, score) VALUES (:quiz_id, :user_name, :date_of_attempt, :score)";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([
            'quiz_id' => $quiz_id,
            'user_name' => $username,
            'date_of_attempt' => $date,
            'score' => $score
          ]);
          
          header("Location:main_page.php");
          $pdo = null;
      }

    	?>


    <!--      FOR BOOTSTRAP-->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    </body>
    </html>