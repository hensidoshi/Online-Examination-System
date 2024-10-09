<?php require_once('../includes/functions.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!loggedStudent()) {
    header('location:../index.php');
} else {
    $student = loggedStudent();
    $class_id = $student['class_id'];
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>

<main class="py-5" id="bg">
    <div class="container-fluid mt-5">
        <div class="row d-flex justify-content-center">
            <?php
            $liveExams = getLiveExams($class_id);
            if ($liveExams) {
                foreach ($liveExams as $exam) {
                    $disable = null;
                    $timeRemaining = timerRemaining($exam['id']);
                    if ($timeRemaining) {
                        if ($timeRemaining <= 0) {
                            $disable = 'disabled';
                        }
                    }
            ?>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="h5 mb-4"><?php echo htmlspecialchars($exam['exam_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                Questions: <?php echo $exam['total_questions'] ?> <br>
                                Marks: <?php echo $exam['total_marks'] ?> <br>
                                Time: <?php echo $exam['total_time'] . " Minutes" ?>
                            </div>
                            <div class="card-footer">
                                <a href="exam.php?id=<?php echo $exam['id'] ?>" class="btn btn-light btn-block <?php echo $disable; ?>">Proceed <i class="fas fa-angle-double-right"></i></a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/end.php') ?>