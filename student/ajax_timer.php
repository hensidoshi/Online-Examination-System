<?php

    require_once('../includes/functions.php');

    $response = new \stdClass();
    $formErrors = array();

    if(isset($_POST['exam_id'])){
        $exam_id = $_POST['exam_id'];
        $student = loggedStudent();
        $student_class_id = $student['class_id'];
        if(!isDataExists('exams', 'id', $exam_id)){
            array_push($formErrors, 'Your inputs are not valid!');
        } else{
            $exam = getExam($exam_id);
            $is_live = $exam['is_live'];
            if($is_live == 1){
                $stmt = $conn->prepare("SELECT * FROM exam_class WHERE exam_id = ? AND class_id = ? LIMIT 1");
                if($stmt){
                    $stmt->bind_param('ii', $exam_id, $student_class_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result){
                        $row = $result->fetch_assoc();
                        if(!$row){
                            array_push($formErrors, 'Exam is not started yet!');
                        }
                    } else{
                        array_push($formErrors, 'Your inputs are not valid!');
                    }
                } else{
                    array_push($formErrors, 'Error. Please try again later');
                }
            } else{
                array_push($formErrors, 'Exam is not live!');
            }
        }
    }

    if(empty($formErrors)){
        $remaining = timerRemaining($exam_id);
        $response -> remaining = $remaining;
    }

    echo json_encode($response);

?>