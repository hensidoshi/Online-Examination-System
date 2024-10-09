<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedTeacher()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    createExam();
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>


<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php require_once('layouts/sidebar.php') ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Create Exam</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Exam name</label>
                                    <input type="text" name="exam" class="form-control" id="name" value="<?php echo isset($_POST['exam']) ? htmlspecialchars($_POST['exam'], ENT_QUOTES, 'UTF-8') : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Total Questions</label>
                                    <input type="text" name="total_questions" class="form-control" id="name" value="<?php echo isset($_POST['total_questions']) ? $_POST['total_questions'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Total Marks</label>
                                    <input type="text" name="total_marks" class="form-control" id="name" value="<?php echo isset($_POST['total_marks']) ? $_POST['total_marks'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Total Time (Minutes)</label>
                                    <input type="text" name="total_time" class="form-control" id="name" value="<?php echo isset($_POST['total_time']) ? $_POST['total_time'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Pass Marks</label>
                                    <input type="text" name="pass_marks" class="form-control" id="name" value="<?php echo isset($_POST['pass_marks']) ? $_POST['pass_marks'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Exam Date</label>
                                    <input type="date" name="date" class="form-control" value="<?php echo isset($_POST['date']) ? $_POST['date'] : null ?>">
                                </div>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                            <button type="submit" name="submit" class="btn btn-primary mt-3 px-3"> <i class="fas fa-paper-plane"></i> Submit</button>

                            <div class="row mt-3">
                                <div class="col">
                                    <?php require_once('../includes/form_errors.php') ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<?php require_once('layouts/end.php') ?>