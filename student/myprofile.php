<?php require_once('../includes/functions.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!loggedStudent()) {
    header('location:../index.php');
} else {
    $student = loggedStudent();
    $class = getClass($student['class_id']);
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>

<main class="py-5" id="bg">
    <div class="container-fluid mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-2 p-3 bg-primary rounded-left">
                <?php
                if ($student['avatar']) {
                    $url = '../uploads/avatars/' . $student['avatar'];
                } else {
                    $url = '../includes/placeholders/150.png';
                }
                ?>
                <img class="img-fluid" src="<?php echo $url; ?>">
            </div>

            <div class="col-md-4 p-3 bg-primary text-white rounded-right">
                <div>Name: <?php echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Class: <?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Roll No: <?php echo htmlspecialchars($student['roll_no'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Username: <?php echo htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/end.php') ?>