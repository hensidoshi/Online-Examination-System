<?php require_once('../includes/functions.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!loggedTeacher()) {
    header('location:../index.php');
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
                <div class="row mt-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Students
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="view_student.php">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Classes
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="class.php">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Exams
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-align-right fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="view_exam.php">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Results
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-poll fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="results.php">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p> <strong> Recently Registered Students: - </strong></p>
                        <?php
                        $students = recentStudents();
                        $students = array_slice($students, 0, 8, true);
                        if ($students) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Roll No</th>
                                            <th>Class</th>
                                            <th>Username</th>
                                            <th>Profile</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($students as $student) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($student['roll_no'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($student['class_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><a href="student_profile.php?student=<?php echo $student['id'] ?>">Visit</a></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<?php require_once('layouts/end.php') ?>