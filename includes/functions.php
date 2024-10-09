<?php

require_once('db.php');
session_start();

//csrf token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

function login()
{
    $role = $username = $password = null;
    global $formErrors;
    $formErrors = array();
    $rolesArray = array('student', 'teacher', 'admin');

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["role"])) {
        array_push($formErrors, 'Role is required!');
    } else {
        if (!in_array($_POST['role'], $rolesArray)) {
            array_push($formErrors, 'Role is invalid!');
        }
    }
    if (empty($_POST["username"])) {
        array_push($formErrors, 'Username is required!');
    }
    if (empty($_POST["password"])) {
        array_push($formErrors, 'Password is required!');
    }
    if (empty($formErrors)) {
        global $conn;
        $role = $_POST["role"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $table = $role . 's';

        $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        if ($stmt) {
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row) {
                    $hashed_password = $row['password'];
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['user_id'] = $row['id'];
                        header('location:' . $role . '/index.php');
                    } else {
                        array_push($formErrors, 'Password is invalid!');
                    }
                } else {
                    array_push($formErrors, 'Username is invalid!');
                }
            } else {
                array_push($formErrors, 'Username or Password is invalid!');
            }
        } else {
            array_push($formErrors, 'Please try again later!');
        }
    }
}

function logout()
{
    session_unset();
    session_destroy();
    header('location:../index.php');
}

function loggedAdmin()
{
    if (isset($_SESSION['user_id'])) {
        global $conn;
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

function loggedTeacher()
{
    if (isset($_SESSION['user_id'])) {
        global $conn;
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                return $row;
            } else {
                return false;
            }
        }
    }
}

function loggedStudent()
{
    if (isset($_SESSION['user_id'])) {
        global $conn;
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                return $row;
            } else {
                return false;
            }
        }
    }
}

function addTeacher()
{
    $name = $mobile = $username = $password = $avatar = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["name"])) {
        array_push($formErrors, 'Name is required!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["name"])) {
            array_push($formErrors, 'Only alphabets and whitespace are allowed for Name!');
        }
        if (strlen($_POST["name"]) > 50) {
            array_push($formErrors, 'Name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["mobile"])) {
        array_push($formErrors, 'Mobile number is required!');
    } else {
        if (!preg_match("/^[0-9]*$/", $_POST["mobile"])) {
            array_push($formErrors, 'Only numeric value is allowed for Mobile number!');
        }
        if (strlen($_POST["mobile"]) < 10 || strlen($_POST["mobile"]) > 10) {
            array_push($formErrors, 'Mobile must have 10 digits!');
        }
        if (isDataExists('teachers', 'mobile', $_POST["mobile"])) {
            array_push($formErrors, 'Mobile number already exists!');
        }
    }

    if (empty($_POST["username"])) {
        array_push($formErrors, 'Username is required!');
    } else {
        if (isDataExists('teachers', 'username', $_POST["username"])) {
            array_push($formErrors, 'Username already taken!');
        }
        if (strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 20) {
            array_push($formErrors, 'Username is must have 6 to 20 Characters!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Password is required!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'Password must have 6 to 20 Characters!');
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Please choose a JPEG or PNG file for avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'File size must be less than 200KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $name = $_POST["name"];
        $mobile = $_POST["mobile"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
        } else {
            $file_name = null;
        }

        $stmt = $conn->prepare("INSERT INTO teachers(name, mobile, username, password, avatar) VALUES(?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sisss', $name, $mobile, $username, $password, $file_name);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Data inserted successfully!';
                header('location:add_teacher.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function editTeacher()
{
    $name = $teacher_id = $mobile = $username = $password = $avatar = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["teacher_id"])) {
        array_push($formErrors, 'Teacher id is required!');
    } else {
        if (!isDataExists('teachers', 'id', $_POST["teacher_id"])) {
            array_push($formErrors, ' Teacher id is invalid!');
        } else {
            $teacher = getTeacher($_POST["teacher_id"]);
        }
    }

    if (empty($_POST["name"])) {
        array_push($formErrors, 'Name is required!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["name"])) {
            array_push($formErrors, 'Only alphabets and whitespace are allowed for Name!');
        }
        if (strlen($_POST["name"]) > 50) {
            array_push($formErrors, 'Name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["mobile"])) {
        array_push($formErrors, 'Mobile number is required!');
    } else {
        if ($teacher['mobile'] != $_POST['mobile']) {
            if (!preg_match("/^[0-9]*$/", $_POST["mobile"])) {
                array_push($formErrors, 'Only numeric value is allowed for Mobile number!');
            }
            if (strlen($_POST["mobile"]) < 10 || strlen($_POST["mobile"]) > 10) {
                array_push($formErrors, 'Mobile must have 10 digits!');
            }
            if (isDataExists('teachers', 'mobile', $_POST["mobile"])) {
                array_push($formErrors, 'Mobile number already exists!');
            }
        }
    }

    if (empty($_POST["username"])) {
        array_push($formErrors, 'Username is required!');
    } else {
        if ($teacher['username'] != $_POST['username']) {
            if (isDataExists('teachers', 'username', $_POST["username"])) {
                array_push($formErrors, 'Username already taken!');
            }
            if (strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 20) {
                array_push($formErrors, 'Username is must have 6 to 20 Characters!');
            }
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Please choose a JPEG or PNG file for avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'File size must be less than 200KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $teacher_id = $_POST['teacher_id'];
        $name = $_POST["name"];
        $mobile = $_POST["mobile"];
        $username = $_POST["username"];

        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
            unlink('../uploads/avatars/' . $teacher['avatar']);
        } else {
            $file_name = $teacher['avatar'];
        }

        $stmt = $conn->prepare("UPDATE teachers SET name = ?, mobile = ?, username = ?, avatar = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('sissi', $name, $mobile, $username, $file_name, $teacher_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Teacher updated successfully!';
                header('location:teacher_profile.php?teacher=' . $teacher_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function isDataExists($table, $column, $data)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $column = ? LIMIT 1");
    $stmt->bind_param('s', $data);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            return true;
        }
    }
    return false;
}

function allTeachers()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM teachers");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result;
        }
    }
    return false;
}

function addClass()
{
    $class = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["class"])) {
        array_push($formErrors, 'Class name is required!');
    } else {
        if (isDataExists('classes', 'name', $_POST["class"])) {
            array_push($formErrors, 'Class already exists!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $class = htmlspecialchars($_POST["class"]);

        $stmt = $conn->prepare("INSERT INTO classes(name) VALUES(?)");

        if ($stmt) {
            $stmt->bind_param('s', $class);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Data inserted successfully!';
                header('location:class.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function allClasses()
{
    global $conn;
    $stmt = $conn->prepare("SELECT classes.*, COUNT(students.id) AS total_student FROM classes LEFT JOIN students ON classes.id=students.class_id GROUP BY classes.id");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result;
        }
    }
    return false;
}

function addStudent()
{
    $name = $class_id = $username = $password = $avatar = $roll_no = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["name"])) {
        array_push($formErrors, 'Name is required!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["name"])) {
            array_push($formErrors, 'Only alphabets and whitespace are allowed for Name!');
        }
        if (strlen($_POST["name"]) > 50) {
            array_push($formErrors, 'Name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["class_id"])) {
        array_push($formErrors, 'Class is required!');
    } else {
        if (!isDataExists('classes', 'id', $_POST["class_id"])) {
            array_push($formErrors, ' Class is invalid!');
        }
    }

    if (empty($_POST["roll_no"])) {
        array_push($formErrors, 'Roll no is required!');
    } else {
        if (isRollnoExists($_POST["class_id"], $_POST["roll_no"])) {
            array_push($formErrors, 'Roll no already exists!');
        }
    }

    if (empty($_POST["username"])) {
        array_push($formErrors, 'Username is required!');
    } else {
        if (isDataExists('students', 'username', $_POST["username"])) {
            array_push($formErrors, 'Username already taken!');
        }
        if (strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 20) {
            array_push($formErrors, 'Username is must have 6 to 20 Characters!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Password is required!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'Password must have 6 to 20 Characters!');
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Please choose a JPEG or PNG file for avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'File size must be less than 200KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $name = $_POST["name"];
        $class_id = $_POST["class_id"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $roll_no = $_POST["roll_no"];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
        } else {
            $file_name = null;
        }

        $stmt = $conn->prepare("INSERT INTO students(name, class_id, roll_no, username, password, avatar) VALUES(?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sissss', $name, $class_id, $roll_no, $username, $password, $file_name);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Data inserted successfully!';
                header('location:add_student.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function editStudent()
{
    $name = $class_id = $student_id = $username = $avatar = $roll_no = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["student_id"])) {
        array_push($formErrors, 'Student id is required!');
    } else {
        if (!isDataExists('students', 'id', $_POST["student_id"])) {
            array_push($formErrors, ' Student id is invalid!');
        } else {
            $student = getStudent($_POST["student_id"]);
        }
    }

    if (empty($_POST["name"])) {
        array_push($formErrors, 'Name is required!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["name"])) {
            array_push($formErrors, 'Only alphabets and whitespace are allowed for Name!');
        }
        if (strlen($_POST["name"]) > 50) {
            array_push($formErrors, 'Name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["class_id"])) {
        array_push($formErrors, 'Class is required!');
    } else {
        if (!isDataExists('classes', 'id', $_POST["class_id"])) {
            array_push($formErrors, ' Class is invalid!');
        }
    }

    if (empty($_POST["roll_no"])) {
        array_push($formErrors, 'Roll no is required!');
    } else {
        if ($student['roll_no'] != $_POST["roll_no"]) {
            if (isRollnoExists($_POST["class_id"], $_POST["roll_no"])) {
                array_push($formErrors, 'Roll no already exists!');
            }
        }
    }

    if (empty($_POST["username"])) {
        array_push($formErrors, 'Username is required!');
    } else {
        if ($student['username'] != $_POST['username']) {
            if (isDataExists('students', 'username', $_POST["username"])) {
                array_push($formErrors, 'Username already taken!');
            }
            if (strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 20) {
                array_push($formErrors, 'Username is must have 6 to 20 Characters!');
            }
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Please choose a JPEG or PNG file for avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'File size must be less than 200KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $student_id = $_POST['student_id'];
        $name = $_POST["name"];
        $class_id = $_POST["class_id"];
        $username = $_POST["username"];
        $roll_no = $_POST["roll_no"];
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
            unlink('../uploads/avatars/' . $student['avatar']);
        } else {
            $file_name = $student['avatar'];
        }

        $stmt = $conn->prepare("UPDATE students SET name = ?, class_id = ?, roll_no = ?, username = ?, avatar = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('sisssi', $name, $class_id, $roll_no, $username, $file_name, $student_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Student updated successfully!';
                header('location:student_profile.php?student=' . $student_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function isRollnoExists($class_id, $roll_no)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM students WHERE class_id = ? AND roll_no = ? LIMIT 1");
    $stmt->bind_param('is', $class_id, $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            return true;
        }
    }
    return false;
}

// Returns 10 recently registered students
function recentStudents()
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT students.*, classes.name AS class_name FROM students INNER JOIN classes ON students.class_id=classes.id ORDER BY students.id DESC LIMIT 10");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Returns 10 recently registered teachers
function recentTeachers()
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT * FROM teachers ORDER BY id DESC LIMIT 10");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Returns a particular class
function getClass($class_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM classes WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// Returns all students of a particular class
function classStudents($class_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM students WHERE class_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result;
        }
    }
    return false;
}

// Create exam
function createExam()
{
    $exam = $total_questions = $total_marks = $total_time = $date = $pass_marks = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["exam"])) {
        array_push($formErrors, 'Exam name is required!');
    } else {
        if (strlen($_POST["exam"]) > 50) {
            array_push($formErrors, 'Exam name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["total_questions"])) {
        array_push($formErrors, 'Total Question is required!');
    } else {
        if (!is_numeric($_POST["total_questions"])) {
            array_push($formErrors, 'Total Question should be numeric!');
        }
    }

    if (empty($_POST["total_marks"])) {
        array_push($formErrors, 'Total Marks is required!');
    } else {
        if (!is_numeric($_POST["total_marks"])) {
            array_push($formErrors, 'Total Marks should be numeric!');
        } else {
            if (empty($_POST["pass_marks"])) {
                array_push($formErrors, 'Pass marks is required!');
            } else {
                if (!is_numeric($_POST["pass_marks"])) {
                    array_push($formErrors, 'Pass Marks should be numeric!');
                } else {
                    if ($_POST["total_marks"] < $_POST["pass_marks"]) {
                        array_push($formErrors, 'Pass Marks should be less than total marks!');
                    }
                }
            }
        }
    }

    if (empty($_POST["total_time"])) {
        array_push($formErrors, 'Total Time is required!');
    } else {
        if (!is_numeric($_POST["total_time"])) {
            array_push($formErrors, 'Total Time should be numeric!');
        }
    }

    if (empty($_POST["date"])) {
        array_push($formErrors, 'Date is required!');
    } else {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST["date"])) {
            array_push($formErrors, 'Date is not valid!');
        } else {
            $dateExploded = explode("-", $_POST["date"]);
            if (count($dateExploded) != 3) {
                array_push($formErrors, 'Date format is not valid!');
            } else {
                $day = $dateExploded[2];
                $month = $dateExploded[1];
                $year = $dateExploded[0];
                if (!checkdate($month, $day, $year)) {
                    array_push($formErrors, 'Date is not valid!');
                }
            }
        }
    }


    if (empty($formErrors)) {
        global $conn;
        $exam = $_POST["exam"];
        $total_questions = $_POST["total_questions"];
        $total_marks = $_POST["total_marks"];
        $total_time = $_POST["total_time"];
        $date = $_POST["date"];
        $pass_marks = $_POST["pass_marks"];
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO exams(exam_name, created_teacher_id, total_questions, total_marks, pass_marks, total_time, date) VALUES(?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('siiiiis', $exam, $user_id, $total_questions, $total_marks, $pass_marks, $total_time, $date);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Exam created successfully!';
                header('location:view_exam.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

// Update exam
function updateExam()
{
    $exam_id = $exam_name = $total_questions = $total_marks = $total_time = $exam_date = $pass_marks = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["exam_name"])) {
        array_push($formErrors, 'Exam name is required!');
    } else {
        if (strlen($_POST["exam_name"]) > 50) {
            array_push($formErrors, 'Exam name can have maximum 50 characters!');
        }
    }

    if (empty($_POST["total_questions"])) {
        array_push($formErrors, 'Total Question is required!');
    } else {
        if (!is_numeric($_POST["total_questions"])) {
            array_push($formErrors, 'Total Question should be numeric!');
        }
    }

    if (empty($_POST["total_marks"])) {
        array_push($formErrors, 'Total Marks is required!');
    } else {
        if (!is_numeric($_POST["total_marks"])) {
            array_push($formErrors, 'Total Marks should be numeric!');
        } else {
            if (empty($_POST["pass_marks"])) {
                array_push($formErrors, 'Pass marks is required!');
            } else {
                if (!is_numeric($_POST["pass_marks"])) {
                    array_push($formErrors, 'Pass Marks should be numeric!');
                } else {
                    if ($_POST["total_marks"] < $_POST["pass_marks"]) {
                        array_push($formErrors, 'Pass Marks should be less than total marks!');
                    }
                }
            }
        }
    }

    if (empty($_POST["total_time"])) {
        array_push($formErrors, 'Total Time is required!');
    } else {
        if (!is_numeric($_POST["total_time"])) {
            array_push($formErrors, 'Total Time should be numeric!');
        }
    }

    if (empty($_POST["exam_date"])) {
        array_push($formErrors, 'Date is required!');
    } else {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST["exam_date"])) {
            array_push($formErrors, 'Date is not valid!');
        } else {
            $dateExploded = explode("-", $_POST["exam_date"]);
            if (count($dateExploded) != 3) {
                array_push($formErrors, 'Date format is not valid!');
            } else {
                $day = $dateExploded[2];
                $month = $dateExploded[1];
                $year = $dateExploded[0];
                if (!checkdate($month, $day, $year)) {
                    array_push($formErrors, 'Date is not valid!');
                }
            }
        }
    }


    if (empty($formErrors)) {
        global $conn;
        $exam_id = $_POST["exam_id"];
        $exam_name = $_POST["exam_name"];
        $total_questions = $_POST["total_questions"];
        $total_marks = $_POST["total_marks"];
        $total_time = $_POST["total_time"];
        $exam_date = $_POST["exam_date"];
        $pass_marks = $_POST["pass_marks"];
        $user_id = $_SESSION['user_id'];


        $stmt = $conn->prepare("SELECT * FROM exams WHERE id = ? AND created_teacher_id = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param('ii', $exam_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE exams SET exam_name = ?, total_questions = ?, total_marks = ?, pass_marks = ?, total_time = ?, date = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('siiiisi', $exam_name, $total_questions, $total_marks, $pass_marks, $total_time, $exam_date, $exam_id);
                    if ($stmt->execute()) {
                        $_SESSION['feedbackSuccess'] = 'Exam data updated successfully!';
                        header('location:view_exam.php');
                        exit();
                    }
                }
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

// Returns all exams created by a particular teacher
function teacherExams($teacher_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM exams WHERE created_teacher_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Insert a question
function addQuestion()
{
    $question = $option_a = $option_b = $option_c = $option_d = $correct_option = $exam_id = $marks = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    $user_id = $_SESSION['user_id'];

    if (empty($_POST["exam_id"])) {
        array_push($formErrors, 'Exam id is required!');
    } else {
        if (!isDataExists('exams', 'id', $_POST["exam_id"])) {
            array_push($formErrors, 'Exam id is invalid!');
        }
        //check exam is created by this teacher or not
        $exams = teacherExams($user_id);
        $valid = false;
        foreach ($exams as $exam) {
            if ($exam['id'] == $_POST["exam_id"]) {
                if ($exam['created_teacher_id'] == $user_id) {
                    $valid = true;
                }
            }
        }
        if (!$valid) {
            array_push($formErrors, 'Exam id is invalid!');
        }

        $check = countQuestionsMarks($_POST["exam_id"]);
        $exam = getExam($_POST["exam_id"]);

        if ($exam['total_questions'] == $check['COUNT(*)']) {
            array_push($formErrors, 'Total Question is exceeded!');
        }
        if ($exam['total_marks'] == $check['SUM(marks)']) {
            array_push($formErrors, 'Total Marks is exceeded!');
        }
    }

    if (!isset($_POST["question"]) || trim($_POST["question"]) == null) {
        array_push($formErrors, 'Question is required!');
    } else {
        if (strlen($_POST["question"]) > 16777215) {
            array_push($formErrors, 'Question can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_a"]) || trim($_POST["option_a"]) == null) {
        array_push($formErrors, 'Option A is required!');
    } else {
        if (strlen($_POST["option_a"]) > 16777215) {
            array_push($formErrors, 'Option A can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_b"]) || trim($_POST["option_b"]) == null) {
        array_push($formErrors, 'Option B is required!');
    } else {
        if (strlen($_POST["option_b"]) > 16777215) {
            array_push($formErrors, 'Option B can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_c"]) || trim($_POST["option_c"]) == null) {
        array_push($formErrors, 'Option C is required!');
    } else {
        if (strlen($_POST["option_c"]) > 16777215) {
            array_push($formErrors, 'Option C can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_d"]) || trim($_POST["option_d"]) == null) {
        array_push($formErrors, 'Option D is required!');
    } else {
        if (strlen($_POST["option_d"]) > 16777215) {
            array_push($formErrors, 'Option D can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["correct_option"])) {
        array_push($formErrors, 'Select correct option!');
    } else {
        $valid_options = array('option_a', 'option_b', 'option_c', 'option_d');
        if (!in_array($_POST["correct_option"], $valid_options)) {
            array_push($formErrors, 'Select a valid correct option!');
        }
    }

    if (empty($_POST["marks"])) {
        array_push($formErrors, 'Marks is required!');
    } else {
        if (!is_numeric($_POST["marks"])) {
            array_push($formErrors, 'Marks should be numeric!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $question = $_POST["question"];
        $option_a = $_POST["option_a"];
        $option_b = $_POST["option_b"];
        $option_c = $_POST["option_c"];
        $option_d = $_POST["option_d"];
        $exam_id = $_POST["exam_id"];
        $marks = $_POST["marks"];
        $correct_option = $_POST["correct_option"];

        $stmt = $conn->prepare("INSERT INTO questions(question, option_a, option_b, option_c, option_d, exam_id, correct_option, marks) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sssssisi', $question, $option_a, $option_b, $option_c, $option_d, $exam_id, $correct_option, $marks);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Question added successfully!';
                header('location:add_questions.php?exam=' . $exam_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

// Update a question
function updateQuestion()
{
    $question = $option_a = $option_b = $option_c = $option_d = $correct_option = $question_id = $marks = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    $user_id = $_SESSION['user_id'];

    if (empty($_POST["question_id"])) {
        array_push($formErrors, 'Question id is required!');
    } else {
        if (!isDataExists('questions', 'id', $_POST["question_id"])) {
            array_push($formErrors, 'Question id is invalid!');
        } else {
            $question = getQuestion($question_id);
            $exam_id = $question['exam_id'];
            $exam = getExam($exam_id);
            if ($exam) {
                if (!$user_id == $exam['created_teacher_id']) {
                    array_push($formErrors, 'You are not authorized to modify this!');
                } else {
                    $check = countQuestionsMarks($exam_id);
                    if ($exam['total_questions'] == $check['COUNT(*)']) {
                        array_push($formErrors, 'Total Question is exceeded!');
                    }
                    if ($exam['total_marks'] == $check['SUM(marks)']) {
                        array_push($formErrors, 'Total Marks is exceeded!');
                    }
                }
            }
        }
    }

    if (!isset($_POST["question"]) || trim($_POST["question"]) == null) {
        array_push($formErrors, 'Question is required!');
    } else {
        if (strlen($_POST["question"]) > 16777215) {
            array_push($formErrors, 'Question can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_a"]) || trim($_POST["option_a"]) == null) {
        array_push($formErrors, 'Option A is required!');
    } else {
        if (strlen($_POST["option_a"]) > 16777215) {
            array_push($formErrors, 'Option A can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_b"]) || trim($_POST["option_b"]) == null) {
        array_push($formErrors, 'Option B is required!');
    } else {
        if (strlen($_POST["option_b"]) > 16777215) {
            array_push($formErrors, 'Option B can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_c"]) || trim($_POST["option_c"]) == null) {
        array_push($formErrors, 'Option C is required!');
    } else {
        if (strlen($_POST["option_c"]) > 16777215) {
            array_push($formErrors, 'Option C can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["option_d"]) || trim($_POST["option_d"]) == null) {
        array_push($formErrors, 'Option D is required!');
    } else {
        if (strlen($_POST["option_d"]) > 16777215) {
            array_push($formErrors, 'Option D can have maximum 16777215 characters!');
        }
    }

    if (!isset($_POST["correct_option"])) {
        array_push($formErrors, 'Select correct option!');
    } else {
        $valid_options = array('option_a', 'option_b', 'option_c', 'option_d');
        if (!in_array($_POST["correct_option"], $valid_options)) {
            array_push($formErrors, 'Select a valid correct option!');
        }
    }

    if (empty($_POST["marks"])) {
        array_push($formErrors, 'Marks is required!');
    } else {
        if (!is_numeric($_POST["marks"])) {
            array_push($formErrors, 'Marks should be numeric!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $question = $_POST["question"];
        $option_a = $_POST["option_a"];
        $option_b = $_POST["option_b"];
        $option_c = $_POST["option_c"];
        $option_d = $_POST["option_d"];
        $question_id = $_POST["question_id"];
        $marks = $_POST["marks"];
        $correct_option = $_POST["correct_option"];

        $questionData = getQuestion($question_id);
        $exam_id = $questionData['exam_id'];

        $stmt = $conn->prepare("UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?, marks = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('ssssssii', $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $marks, $question_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Question updated successfully!';
                header('location:view_questions.php?exam=' . $exam_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

// Returns a particular exam
function getExam($exam_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM exams WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// Returns all questions of a particular exam
function getQuestions($exam_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Returns number of inserted questions of a particular exam
function countQuestionsMarks($exam_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*), SUM(marks) FROM questions WHERE exam_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// View inserted questions by a particular teacher
function viewQuestions($exam_id)
{
    $user_id = $_SESSION['user_id'];
    $exams = teacherExams($user_id);
    $valid = false;
    foreach ($exams as $exam) {
        if ($exam['id'] == $exam_id) {
            if ($exam['created_teacher_id'] == $user_id) {
                $valid = true;
            }
        }
    }
    if ($valid) {
        return getQuestions($exam_id);
    } else {
        return false;
    }
}

// Assign exam to class
function assignExam()
{
    $exam_id = $class_id = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    $user_id = $_SESSION['user_id'];

    if (empty($_POST["exam_id"])) {
        array_push($formErrors, 'Exam is required!');
    } else {
        if (!isDataExists('exams', 'id', $_POST["exam_id"])) {
            array_push($formErrors, 'Exam id is invalid!');
        }
        //check exam is created by this teacher or not
        $exams = teacherExams($user_id);
        $valid = false;
        foreach ($exams as $exam) {
            if ($exam['id'] == $_POST["exam_id"]) {
                if ($exam['created_teacher_id'] == $user_id) {
                    $valid = true;
                }
            }
        }
        if (!$valid) {
            array_push($formErrors, 'Exam id is invalid!');
        }
    }

    if (empty($_POST["class_id"])) {
        array_push($formErrors, 'Class is required!');
    } else {
        if (!isDataExists('classes', 'id', $_POST["class_id"])) {
            array_push($formErrors, 'Class id is invalid!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $exam_id = $_POST["exam_id"];
        $class_id = $_POST["class_id"];

        // Check if already exists
        $stmt = $conn->prepare("SELECT id FROM exam_class WHERE exam_id = ? AND class_id = ?");
        if ($stmt) {
            $stmt->bind_param('ii', $exam_id, $class_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row) {
                    $_SESSION['feedbackFailed'] = 'Failed! Exam is already assigned to class.';
                    header('location:assign_exam.php');
                    exit();
                } else {
                    $stmt = $conn->prepare("INSERT INTO exam_class(exam_id, class_id) VALUES(?, ?)");

                    if ($stmt) {
                        $stmt->bind_param('ii', $exam_id, $class_id);
                        if ($stmt->execute()) {
                            $_SESSION['feedbackSuccess'] = 'Exam assigned to class successfully!';
                            header('location:assign_exam.php');
                            exit();
                        } else {
                            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                        }
                    } else {
                        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                    }
                }
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        }
    }
}

// Get all assigned exams by a particular teacher
function getAssignedExams()
{
    //To do 
    //Prevent same exam_id & class_id insert in assign_exam()
    $user_id = $_SESSION['user_id'];
    $rows = array();
    global $conn;

    $stmt = $conn->prepare("SELECT exams.exam_name AS exam_name, exams.id AS exam_id, exams.date, classes.name AS class_name, 
        classes.id AS class_id FROM exams INNER JOIN exam_class ON exams.id = exam_class.exam_id INNER JOIN classes ON 
        classes.id = exam_class.class_id WHERE exams.created_teacher_id = ?");

    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Get all live exams of a particular class
function getLiveExams($class_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT exams.* FROM exams INNER JOIN exam_class ON exams.id = exam_class.exam_id WHERE exam_class.class_id = ? AND exams.is_live = 1");
    if ($stmt) {
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Returns a particular question
function getQuestion($question_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM questions WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Returns all answers of an exam for a student
function getAnswers($exam_id, $student_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT answers.*, questions.* FROM answers INNER JOIN questions ON answers.question_id = questions.id WHERE questions.exam_id = ? AND answers.student_id = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $exam_id, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
}

// Start the timer for logged student
function startTimer($exam_id)
{
    global $conn;
    $student_id = $_SESSION['user_id'];

    if (isDataExists('exams', 'id', $exam_id)) {
        if (isValidExamClass($exam_id)) {
            // check if timer exists
            if (!getTimer($exam_id)) {
                // Insert the timer

                $stmt = $conn->prepare("INSERT INTO timer(student_id, exam_id, start_time) VALUES(?, ?, ?)");
                if ($stmt) {
                    $time = time();
                    $stmt->bind_param('iis', $student_id, $exam_id, $time);
                    $stmt->execute();
                }
            }
        }
    }
}

// Check if exam is valid for logged student
function isValidExamClass($exam_id)
{
    global $conn;
    $student = loggedStudent();
    $student_class_id = $student['class_id'];
    $exam = getExam($exam_id);
    $is_live = $exam['is_live'];
    if ($is_live == 1) {
        $stmt = $conn->prepare("SELECT * FROM exam_class WHERE exam_id = ? AND class_id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('ii', $exam_id, $student_class_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                if ($row) {
                    return true;
                }
            }
        }
    }
    return false;
}

// Returns timer of logged student for an exam
function getTimer($exam_id)
{
    $student_id = $_SESSION['user_id'];
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM timer WHERE student_id = ? AND exam_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('ii', $student_id, $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Returns remaining time of logged student for an exam
function timerRemaining($exam_id)
{
    $student_id = $_SESSION['user_id'];
    $timer = getTimer($exam_id);
    if ($timer) {
        $time = time();
        $exam = getExam($exam_id);

        $total_time = $exam['total_time'] * 60;
        $spent = $time - $timer['start_time'];
        $remaining = $total_time - $spent;

        return $remaining;

    }
    return false;
}

// Returns results
function results($class_id, $exam_id)
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT SUM(CASE WHEN answers.answered_option=questions.correct_option THEN questions.marks ELSE 0 END) AS obtain, students.id AS student_id, students.name AS student_name, students.roll_no FROM questions INNER JOIN answers ON answers.question_id = questions.id INNER JOIN students ON answers.student_id=students.id WHERE questions.exam_id = ? AND students.class_id = ? GROUP BY answers.student_id");
    if ($stmt) {
        $stmt->bind_param('ii', $exam_id, $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        } else {
            return false;
        }
    }
}

// Check if accessing results is valid for logged teacher
function isValidResultAccessTeacher($exam_id, $class_id)
{
    global $conn;
    $teacher_id = $_SESSION['user_id'];
    $exam = getExam($exam_id);
    if ($exam) {
        if ($teacher_id == $exam['created_teacher_id']) {
            $stmt = $conn->prepare("SELECT * FROM exam_class WHERE exam_id = ? AND class_id = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('ii', $exam_id, $class_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    if ($row) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    return false;
}

// Returns a student
function getStudent($student_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Returns a teacher
function getTeacher($teacher_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}


// Deletes a exam by a teacher
function deleteExamTeacher($exam_id)
{
    global $conn;
    $teacher_id = $_SESSION['user_id'];
    $exam = getExam($exam_id);

    if ($exam) {
        if ($teacher_id == $exam['created_teacher_id']) {
            $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('i', $exam_id);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Exam deleted successfully!';
                    header('location:view_exam.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
}

// Deletes a assigned exam by a teacher
function deleteAssignedExam($exam_id, $class_id)
{
    global $conn;
    $teacher_id = $_SESSION['user_id'];
    $exam = getExam($exam_id);

    if ($exam) {
        if ($teacher_id == $exam['created_teacher_id']) {
            $stmt = $conn->prepare("DELETE FROM exam_class WHERE exam_id = ? AND class_id = ?");
            if ($stmt) {
                $stmt->bind_param('ii', $exam_id, $class_id);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Assigned Exam deleted successfully!';
                    header('location:assign_exam.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
}


function getExamsAdmin()
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT exams.*, teachers.name FROM exams LEFT JOIN teachers ON exams.created_teacher_id = teachers.id");
    if ($stmt) {
        // $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Returns all assigned classes for a particular exam 
function getAssignClasses($exam_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT classes.* FROM classes INNER JOIN exam_class ON exam_class.class_id = classes.id WHERE exam_class.exam_id = ? ORDER BY classes.name");
    if ($stmt) {
        $stmt->bind_param('i', $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Live a exam
function makeLive($exam_id)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE exams SET is_live = CASE WHEN is_live = 0 THEN 1 ELSE 0 END WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $exam_id);
        if ($stmt->execute()) {
            $_SESSION['feedbackSuccess'] = 'Access Updated successfully!';
            header('location:exam.php');
            exit();
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
}

function getAssignedExamsAdmin()
{
    $rows = array();
    global $conn;

    $stmt = $conn->prepare("SELECT exams.exam_name AS exam_name, exams.id AS exam_id, exams.date, classes.name AS class_name, 
        classes.id AS class_id FROM exams INNER JOIN exam_class ON exams.id = exam_class.exam_id INNER JOIN classes ON 
        classes.id = exam_class.class_id");

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Deletes a question by a teacher
function deleteQuestion($question_id)
{
    global $conn;
    $teacher_id = $_SESSION['user_id'];
    $question = getQuestion($question_id);

    if (isset($question['exam_id'])) {
        $exam_id = $question['exam_id'];
        $exam = getExam($exam_id);
    }

    if (isset($exam)) {
        if ($teacher_id == $exam['created_teacher_id']) {
            $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('i', $question_id);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Question deleted successfully!';
                    header('location:view_questions.php?exam=' . $exam_id);
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
    $_SESSION['feedbackFailed'] = 'Failed! Question not found!.';
}

function deleteTeacher($teacher_id)
{
    global $conn;
    if (isDataExists('teachers', 'id', $teacher_id)) {
        $teacher = getTeacher($teacher_id);

        $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $teacher_id);
            if ($stmt->execute()) {
                if ($teacher['avatar']) {
                    unlink('../uploads/avatars/' . $teacher['avatar']);
                }
                $_SESSION['feedbackSuccess'] = 'Teacher deleted successfully!';
                header('location:view_teacher.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
}

function deleteStudent($student_id)
{
    global $conn;
    if (isDataExists('students', 'id', $student_id)) {
        $student = getStudent($student_id);
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $student_id);
            if ($stmt->execute()) {
                if ($student['avatar']) {
                    unlink('../uploads/avatars/' . $student['avatar']);
                }
                $_SESSION['feedbackSuccess'] = 'Student deleted successfully!';
                header('location:view_student.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
}

function assignTeacher()
{
    $exam_id = $teacher_id = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["exam_id"])) {
        array_push($formErrors, 'Exam id is required!');
    } else {
        if (!isDataExists('exams', 'id', $_POST["exam_id"])) {
            array_push($formErrors, 'Exam id is invalid!');
        }
    }

    if (empty($_POST["teacher_id"])) {
        array_push($formErrors, 'Teacher id is required!');
    } else {
        if (!isDataExists('teachers', 'id', $_POST["teacher_id"])) {
            array_push($formErrors, 'Teacher id is invalid!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $teacher_id = $_POST["teacher_id"];
        $exam_id = $_POST["exam_id"];

        $stmt = $conn->prepare("UPDATE exams SET created_teacher_id = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('ii', $teacher_id, $exam_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Teacher assign successfully!';
                header('location:exam.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function deleteClass($class_id)
{
    global $conn;
    if (isDataExists('classes', 'id', $class_id)) {
        $stmt1 = $conn->prepare("DELETE FROM classes WHERE id = ?");
        $stmt2 = $conn->prepare("SELECT avatar FROM students WHERE class_id = ?");
        if ($stmt1 && $stmt2) {
            $stmt1->bind_param('i', $class_id);
            $stmt2->bind_param('i', $class_id);
            if ($stmt1->execute()) {
                $stmt2->execute();
                $result = $stmt2->get_result();
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['avatar']) {
                            unlink('../uploads/avatars/' . $row['avatar']);
                        }
                    }
                }
                $_SESSION['feedbackSuccess'] = 'Class deleted successfully!';
                header('location:class.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
    }
}

function editClass()
{
    $class = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["class_name"])) {
        array_push($formErrors, 'Class name is required!');
    } else {
        if (isDataExists('classes', 'name', $_POST["class"])) {
            array_push($formErrors, 'Class already exists!');
        }
    }

    if (empty($_POST["class_id"])) {
        array_push($formErrors, 'Class id is required!');
    } else {
        if (!isDataExists('classes', 'id', $_POST["class_id"])) {
            array_push($formErrors, 'Class id is invalid!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $class = htmlspecialchars($_POST["class_name"]);
        $id = htmlspecialchars($_POST["class_id"]);

        $stmt = $conn->prepare("UPDATE classes SET name = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $class, $id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Class updated successfully!';
                header('location:class.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function changeStudentPasswordByAdmin()
{
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["student_id"])) {
        array_push($formErrors, 'Student id is required!');
    } else {
        if (!isDataExists('students', 'id', $_POST["student_id"])) {
            array_push($formErrors, ' Student id is invalid!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Password is required!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'Password must have 6 to 20 Characters!');
        }
    }
    if (empty($formErrors)) {
        global $conn;
        $student_id = $_POST['student_id'];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $password, $student_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Password updated successfully!';
                header('location:student_profile.php?student=' . $student_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

function changeTeacherPasswordByAdmin()
{
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if (empty($_POST["teacher_id"])) {
        array_push($formErrors, 'Teacher id is required!');
    } else {
        if (!isDataExists('teachers', 'id', $_POST["teacher_id"])) {
            array_push($formErrors, 'Teacher id is invalid!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Password is required!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'Password must have 6 to 20 Characters!');
        }
    }
    if (empty($formErrors)) {
        global $conn;
        $teacher_id = $_POST['teacher_id'];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE teachers SET password = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $password, $teacher_id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Password updated successfully!';
                header('location:teacher_profile.php?teacher=' . $teacher_id);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
        }
    }
}

// profile results
function profileResults($student_id)
{
    global $conn;
    $rows = array();


    $stmt = $conn->prepare("SELECT SUM(CASE WHEN answers.answered_option=questions.correct_option THEN questions.marks ELSE 0 END) AS obtain, exams.exam_name, exams.total_marks, exams.pass_marks FROM exams INNER JOIN questions ON exams.id=questions.exam_id INNER JOIN answers ON answers.question_id=questions.id WHERE answers.student_id = ? GROUP BY exams.id");



    if ($stmt) {
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        } else {
            return false;
        }
    }
}

function changeMyPassword($table)
{
    global $formErrors;
    global $conn;
    $formErrors = array();
    $user_id = $_SESSION['user_id'];

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'CSRF Token is required!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'CSRF Token invalid!');
        }
    }

    if ($user_id) {
        if (empty($_POST["current_password"])) {
            array_push($formErrors, 'Current password is required!');
        } else {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $hashed_password = $row['password'];
                    if (!password_verify($_POST["current_password"], $hashed_password)) {
                        array_push($formErrors, 'Current Password not matching!');
                    }
                } else {
                    array_push($formErrors, 'Something is wrong!');
                }
            }
        }

        if (empty($_POST["password"])) {
            array_push($formErrors, 'Password is required!');
        } else {
            if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
                array_push($formErrors, 'Password must have 6 to 20 Characters!');
            } else {
                if (empty($_POST["confirm_password"])) {
                    array_push($formErrors, 'Confirm Password is required!');
                } else {
                    if ($_POST["password"] != $_POST["confirm_password"]) {
                        array_push($formErrors, 'Password & Confirm Password not matching!');
                    }
                }
            }
        }

        if (empty($formErrors)) {
            global $conn;
            $password = $_POST["password"];
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('si', $password, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Password updated successfully!';
                    header('location:index.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
                }
            } else {
                $_SESSION['feedbackFailed'] = 'Failed! Please try again later.';
            }
        } else {
            $passwordErrors = null;
            foreach ($formErrors as $errors) {
                $passwordErrors = $passwordErrors . $errors . '<br>';
            }
            $_SESSION['feedbackFailed'] = $passwordErrors;
        }
    }
}

//get all timers of a particular students
function getTimers($student_id)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM timer WHERE student_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// get result for an exam for a particular student
function examResult($student_id, $exam_id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT SUM(CASE WHEN answers.answered_option=questions.correct_option THEN questions.marks ELSE 0 END) AS obtain, exams.exam_name, exams.total_marks, exams.pass_marks, exams.date FROM exams INNER JOIN questions ON exams.id=questions.exam_id INNER JOIN answers ON answers.question_id=questions.id WHERE answers.student_id = ? AND exams.id = ? GROUP BY exams.id");

    if ($stmt) {
        $stmt->bind_param('ii', $student_id, $exam_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            if(isset($row)){
                return $row;
            }
            else {
                $stmt = $conn->prepare("SELECT '0' AS obtain, exams.exam_name, exams.total_marks, exams.pass_marks, exams.date FROM exams WHERE exams.id = ? ");

                if($stmt){
                    $stmt->bind_param('i', $exam_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        $row = $result->fetch_assoc();
                        return $row;
                    }
                }
            }
        }
    }
}

// Get result for this exam for this student
function getMyResults()
{
    $rows = array();
    $user_id = $_SESSION['user_id'];
    $timers = getTimers($user_id);

    foreach ($timers as $timer) {
        $exam_id = $timer['exam_id'];
        $timeRemaining = timerRemaining($exam_id);

        if ($timeRemaining <= 0) {
            $result = examResult($user_id, $exam_id);
            if($result){
                array_push($rows, $result);
            }
        }
    }
    return $rows;
}
