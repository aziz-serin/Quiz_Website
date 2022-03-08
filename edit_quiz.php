<!DOCTYPE html>
  <html lang="en">
    <head>
      <title>Quiz Website/EDIT</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">


      <h1>EDIT QUIZ</h1>
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
	    if(!$_SESSION['is_admin'])
          header("Location: main_page.php");
        if (!isset($_GET['quiz_id']))
          header("Location: main_page.php");
      
          $var = $_GET['quiz_id'];
          
          if(empty($_POST)){
              output_questions($var);
          }
          else{
              make_changes($var);
          }

	    function connect_DB(){
          $servername = "localhost";
          $username = "testing";
          $password = "testing";
      
          try {
            $conn = new PDO("mysql:host=$servername;dbname=quiz_app", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
          } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
          }
        }
        
        function output_questions($quiz_id){
          $pdo = connect_DB();
          
          $sql = "SELECT quiz_name, quiz_duration  FROM quiz WHERE quiz_id = $quiz_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $stmt->setFetchMode(PDO::FETCH_ASSOC);
          $row = $stmt->fetch();
          $quiz_name = $row['quiz_name'];
          $quiz_duration = $row['quiz_duration'];
          
          $sql = "SELECT question, question_id FROM question WHERE quiz_id = :quiz_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([
                  'quiz_id' => $quiz_id
          ]);
          $stmt->setFetchMode(PDO::FETCH_ASSOC);
          echo("<form method='POST'><br>
                <div class = 'form-group'>
                    <label for='quizname'>Quiz Name</label>
                    <br>
                    <small id='quizname_help' class='form-text text-muted'>(max length 50)</small>
                    <br>
                    <input type='text' required='required' id='quizname' name='quizname' maxlength='50' value='$quiz_name'>
                    <br>
                </div>
                <div class = 'form-group'>
                    <label for='time'>Enter the duration</label>
                    <br>
                    <small id='time_help' class='form-text text-muted'>(in the format hh:mm:ss)</small>
                    <br>
                    <input type='text' required='required' id='time' name='time' maxlength='8' value='$quiz_duration'>
                    <br>
                </div>");
          
          while($row = $stmt->fetch()){
            $question_id = $row['question_id'];
            $id = "q" . strval($question_id);
            $question_answer = "a" . strval($question_id);
            
            // Echo the question first
            echo("
                <textarea id='$id' name='$id' required='required' rows='5' cols='60'>$row[question]</textarea>
                <br>");
            
            
            
            $sql = "SELECT answer, is_true FROM answers WHERE question_id = :question_id";
            $stmt1 = $pdo->prepare($sql);
            $stmt1->execute([
              'question_id' => $question_id
            ]);
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $i = 1;
            while($values = $stmt1->fetch()){
              $ans_id = "a" . strval($question_id) . strval($i);
              $ans_id_tf = "a" . strval($question_id) . strval($i) . "tf";
              if($values['is_true'] == "1") {
                // if true output it checked
                echo("<div class = 'form-group'>
                    <input type='radio' name = '$question_answer' value='$ans_id_tf' checked>
                    <textarea id='$ans_id' name='$ans_id' required='required' rows='3' cols='50'>$values[answer]</textarea>
                    <br>
                    </div>");
              }
              else{
                  // if not true output it without checked
                echo("<div class = 'form-group'>
                    <input type='radio' name = '$question_answer' value='$ans_id_tf'>
                    <textarea id='$ans_id' name='$ans_id' required='required' rows='3' cols='50'>$values[answer]</textarea>
                    <br>
                    </div>");
              }
              $i++;
            }
          }
          $sql = "SELECT quiz_available FROM quiz WHERE quiz_id = $quiz_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          
          $stmt->setFetchMode(PDO::FETCH_ASSOC);
          $row = $stmt->fetch();
          $is_available = $row['quiz_available'];
          
          if($is_available == "1"){
              echo("<label for='unavailable'>Make the quiz unavailable</label>
                <input type='checkbox' id='unavailable' name='unavailable'>
                <br>");
          }
          else{
            echo("<label for='available'>Make the quiz available</label>
                <input type='checkbox' id='available' name='available'>
                <br>");
          }
          
          echo('<button type = "submit" name = "action" value = "Update" class = "btn btn-warning"> Submit Changes </button>
          <!-- Modal for delete -->
          <div class="topleftcorner">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Delete</button>
          </div>
          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete this quiz?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type = "submit" name = "action" class = "btn btn-danger" value = "Delete"> Delete Quiz </button>
              </div>
            </div>
          </div>
        </div>
        </form>');
          $pdo = null;
        }
        function make_changes($quiz_id)
        {
          if ($_POST['action'] == 'Update') {
            update($quiz_id);
          } else if ($_POST['action'] == 'Delete') {
            delete($quiz_id);
          }
        }
        function update($quiz_id){
              // Update the quiz info
              $quiz_name = $_POST['quizname'];
              $time = preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $_POST['time']);
              if(!$time){
                echo("Check your time format!");
                output_questions($quiz_id);
              }
              $quiz_duration = $_POST['time'];
  
              $pdo = connect_DB();
              
              if(isset($_POST['available'])){
                  $available = "1";
                $sql = "UPDATE quiz SET quiz_name = :quizName, quiz_duration = :quizDuration, quiz_available = :available WHERE quiz_id = :quizId";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                        'quizName' => $quiz_name,
                        'quizDuration' => $quiz_duration,
                        'available' => $available,
                        'quizId' => $quiz_id
                ]);
              }
              else if(isset($_POST['unavailable'])){
                  $available = "0";
                $sql ="UPDATE quiz SET quiz_name = :quizName, quiz_duration = :quizDuration, quiz_available = :available WHERE quiz_id = :quizId";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                  'quizName' => $quiz_name,
                  'quizDuration' => $quiz_duration,
                  'available' => $available,
                  'quizId' => $quiz_id
                ]);
              }
              else{
                $sql = "UPDATE quiz SET quiz_name = :quizName, quiz_duration = :quizDuration WHERE quiz_id = :quizId";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                  'quizName' => $quiz_name,
                  'quizDuration' => $quiz_duration,
                  'quizId' => $quiz_id
                ]);
              }
              
              //Update the questions and answers
              $sql = "SELECT MIN(question_id), MAX(question_id) FROM question WHERE quiz_id = $quiz_id";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();
              $stmt->setFetchMode(PDO::FETCH_ASSOC);
              $row = $stmt->fetch();
              $smallest_qeustion_id = $row['MIN(question_id)'];
              $largest_quesiton_id = $row['MAX(question_id)'];
              
              
              $i = $smallest_qeustion_id;
              while($smallest_qeustion_id <= $largest_quesiton_id){
                  $question_id = $smallest_qeustion_id;
                  $id = "q" . strval($question_id);
                  $question_answer = "a" . strval($question_id);
                  
                  $question_text = $_POST[$id];
                  
                  // update quesiton
                  $sql = "UPDATE question SET question = :question WHERE quiz_id = :quizId AND question_id = :questionID";
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute([
                    'question' => $question_text,
                    'quizId' => $quiz_id,
                    'questionID' => $smallest_qeustion_id
                  ]);
                  // update answers
                  $counter = 1;
                  while($counter < 5){
                    $ans_id = "a" . strval($question_id) . strval($counter);
                    $ans_id_tf = "a" . strval($question_id) . strval($counter) . "tf";
                    $is_true = "0";
                    if($_POST[$question_answer] == $ans_id_tf){
                      $is_true = "1";
                    }
                    $sql = "UPDATE answers SET answer = :answerC, is_true = :TF WHERE question_id = :questionID AND answer_id = :answerID";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                            'answerC' => $_POST[$ans_id],
                            'TF' => $is_true,
                            'questionID' => $question_id,
                            'answerID' => $counter
                    ]);
                    $counter++;
                  }
                $smallest_qeustion_id++;
                $i++;
              }
              $pdo = null;
              header("Location:main_page.php");
        }
        function delete($quiz_id){
          $pdo = connect_DB();
          // First delete attempts, then the question, and then the quiz itself
          $sql = "DELETE FROM attempt WHERE quiz_id = :quizID";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['quizID'=>$quiz_id]);
          
          $quiz_name = $_POST['quizname'];
          $sql = "SELECT MIN(question_id), MAX(question_id) FROM question WHERE quiz_id = $quiz_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $stmt->setFetchMode(PDO::FETCH_ASSOC);
          $row = $stmt->fetch();
          $smallest_question_id = $row['MIN(question_id)'];
          $largest_question_id = $row['MAX(question_id)'];
          while($smallest_question_id <= $largest_question_id){
            $i = 1;
            while($i < 5){
              $sql = "DELETE FROM answers WHERE question_id = :questionID AND answer_id = :aID";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([
                  'questionID' => $smallest_question_id,
                  'aID' => $i
              ]);
              $i++;
            }
            $sql = "DELETE FROM question WHERE question_id = :questionID AND quiz_id = :quizID";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
              'questionID' => $smallest_question_id,
              'quizID' => $quiz_id
            ]);
            $smallest_question_id++;
          }
          
          $sql = "DELETE FROM quiz WHERE quiz_id = :quizID";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([
            'quizID' => $quiz_id
          ]);
          
          $pdo = null;
          header("Location:main_page.php");
        }
        
      ?>

        <!--      FOR BOOTSTRAP-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>



    </body>
    </html>