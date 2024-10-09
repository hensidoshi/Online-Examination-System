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
            <div class="col-md-8 text-center">

                <?php
                $results = getMyResults();
                    if ($results) {

                    ?>

                    <table class="table table-bordered bg-white">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Exam Name</th>
                                <th>Exam Date</th>
                                <th>Total Marks</th>
                                <th>Marks Obtain</th>
                                <th>Result</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach ($results as $result) {
                            ?>
                                <tr class="text-dark">
                                    <td><?php echo htmlspecialchars($result['exam_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?php echo $result['date'] ?></td>
                                    <td><?php echo $result['total_marks'] ?></td>
                                    <td><?php echo $result['obtain'] ?></td>
                                    <td><?php echo (isset($result['obtain']) && ($result['obtain'] >= $result['pass_marks'])) ? '<span class="text-success">Passed</span>' : '<span class="text-danger">Failed</span>' ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                <?php } else { ?>
                    <div class="alert alert-secondary">
                        <h2>Sorry, No results found!</h2>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/end.php') ?>