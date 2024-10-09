<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['makelive']) && isset($_POST['exam_id'])) {
    makeLive($_POST['exam_id']);
}
if (isset($_POST['assign'])) {
    assignTeacher();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Exams</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                All Exams
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $exams = getExamsAdmin();
                        if ($exams) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam</th>
                                            <th>Created by</th>
                                            <th>Class</th>
                                            <th>Date</th>
                                            <th>live</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($exams as $exam) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exam['exam_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php
                                                    if (!$exam['name']) {
                                                    ?>
                                                        <span class="text-warning hover-pointer" data-toggle="modal" data-target="#addModal" data-exam-id="<?php echo $exam['id'] ?>">Teacher not available! Link now.</span>
                                                    <?php
                                                    } else {
                                                        echo htmlspecialchars($exam['name'], ENT_QUOTES, 'UTF-8');;
                                                    ?>
                                                        <span class="text-warning float-right hover-pointer" data-toggle="modal" data-target="#addModal" data-exam-id="<?php echo $exam['id'] ?>">Change</span>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $classes = getAssignClasses($exam['id']);
                                                    foreach ($classes as $class) {
                                                        echo '<span class="badge badge-light mr-2 font-weight-bolder">' . htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($exam['date'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="exam_id" value="<?php echo $exam['id'] ?>">
                                                        <input type="hidden" name="makelive">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                                        <input type="checkbox" onChange="this.form.submit()" <?php echo $exam['is_live'] ? 'checked' : null ?> data-toggle="toggle" data-onstyle="success" data-size="xs">
                                                    </form>
                                                </td>
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

<!-- Modal -->
<div class="modal fade" id="addModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Assign Teacher</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="exam_id">
                    <div class="form-group">
                        <label for="" class="small">Teacher</label>
                        <select class="form-control" name="teacher_id">
                            <?php
                            $teachers = allTeachers();
                            if ($teachers) {
                                foreach ($teachers as $teacher) {
                                    echo '<option value="' . $teacher['id'] . '">' . htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($teacher['username'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="assign" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#addModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var exam_id = button.data('exam-id')
        var modal = $(this)
        modal.find('input[name="exam_id"]').val(exam_id)
    })

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<?php require_once('layouts/end.php') ?>