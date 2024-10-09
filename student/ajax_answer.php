<?php 
    require_once('../includes/functions.php');

    $response = new \stdClass();
    $formErrors = array();
    
    if(isset($_POST['question_id']) && isset($_POST['selected_option'])){
        $user_id = $_SESSION['user_id'];
        $question_id = $_POST['question_id'];
        $selected_option = $_POST['selected_option'];
        $valid_options = array('option_a', 'option_b', 'option_c', 'option_d', 'skip');

        if(!in_array($selected_option, $valid_options)){
            array_push($formErrors, 'Selected option is not valid');
        }

        if(!isDataExists('questions', 'id', $question_id)){
            array_push($formErrors, 'Question id is not valid');
        } else{
            $student = loggedStudent();
            $student_class_id = $student['class_id'];

            $question = getQuestion($question_id);
            $question_exam_id = $question['exam_id'];

            $stmt = $conn->prepare("SELECT * FROM exam_class WHERE exam_id = ? AND class_id = ? LIMIT 1");
            if($stmt){
                $stmt->bind_param('ii', $question_exam_id, $student_class_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result){
                    $row = $result->fetch_assoc();
                    if(!$row){
                        array_push($formErrors, 'Your inputs are not valid!');
                    }
                } else{
                    array_push($formErrors, 'Your inputs are not valid!');
                }
            } else{
                array_push($formErrors, 'Error. Please try again later');
            }
        }

        if(empty($formErrors)){
            //check if time is not over
            if(timerRemaining($question_exam_id) > 0){
            //if answer already exists, update else insert
                $stmt = $conn->prepare("SELECT * FROM answers WHERE student_id = ? AND question_id = ?");
                if($stmt){
                    $stmt->bind_param('ii', $user_id, $question_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result){
                        $row = $result->fetch_assoc();
                        if($row){
                            //update
                            $stmt = $conn->prepare("UPDATE answers SET answered_option = ? WHERE student_id = ? AND question_id = ?");
                            if($stmt){
                                $stmt->bind_param('sii', $selected_option, $user_id, $question_id);
                                if($stmt->execute()){
                                    $response->message = 'Updated';
                                    $response->question_id = $question_id;
                                    $response->selected_option = $selected_option;
                                }
                            } else{
                                array_push($formErrors, 'Error. Please try again later');
                            }
                        } else{
                            //insert
                            $stmt = $conn->prepare("INSERT INTO answers(student_id, question_id, answered_option) VALUES(?, ? ,?)");
                            if($stmt){
                                $stmt->bind_param('iis', $user_id, $question_id, $selected_option);
                                if($stmt->execute()){
                                    $response->message = 'Inserted';
                                    $response->question_id = $question_id;
                                    $response->selected_option = $selected_option;
                                }
                            } else{
                                array_push($formErrors, 'Error. Please try again later');
                            }
                        }
                    } else{
                        array_push($formErrors, 'Your inputs are not valid!');
                    }
                }
            }
        }
    } else{
        array_push($formErrors, 'Your inputs are not valid!');
    }

    $response->formErrors = $formErrors;
    echo json_encode($response);
?>
