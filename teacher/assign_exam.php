<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedTeacher()) {
    header('location:../index.php');
} else {
    $user_id = $_SESSION['user_id'];
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    assignExam();
}
if (isset($_POST['delete']) && isset($_POST['exam_id']) && isset($_POST['class_id'])) {
    deleteAssignedExam($_POST['exam_id'], $_POST['class_id']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Assign Exam</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Select Exam</label>
                                    <select class="form-control" id="exam" name="exam_id">
                                        <option disabled selected>select</option>
                                        <?php
                                        $exams = teacherExams($user_id);
                                        if ($exams) {
                                            foreach ($exams as $exam) {
                                                $selected = null;
                                                if (isset($_POST['exam_id'])) {
                                                    if ($_POST['exam_id'] == $exam['id']) {
                                                        $selected = 'selected';
                                                    }
                                                }
                                                echo "<option value='{$exam['id']}' {$selected}>" . htmlspecialchars($exam['exam_name'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                    <label for="name" class="small">Select Class</label>
                                    <div class="input-group mb-3">
                                        <select class="form-control" id="class" name="class_id">
                                            <option disabled selected>select</option>
                                            <?php
                                            $classes = allClasses();
                                            if ($classes) {
                                                foreach ($classes as $class) {
                                                    $selected = null;
                                                    if (isset($_POST['class_id'])) {
                                                        if ($_POST['class_id'] == $class['id']) {
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                    echo "<option value='{$class['id']}' {$selected}>" . htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary px-3" name="submit" type="submit"><i class="fas fa-paper-plane"></i> Assign</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <?php require_once('../includes/form_errors.php') ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Assigned Exams
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $assignedExams = getAssignedExams();
                        if ($assignedExams) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam</th>
                                            <th>Class</th>
                                            <th>Questions</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($assignedExams as $assign) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($assign['exam_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($assign['class_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><a href="view_questions.php?exam=<?php echo $assign['exam_id'] ?>">View Questions</a></td>
                                                <td><i class="fas fa-trash hover-pointer" data-toggle="modal" data-target="#deleteModal" data-exam-id="<?php echo $assign['exam_id'] ?>" data-class-id="<?php echo $assign['class_id'] ?>"></i></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                        } else {
                        ?>
                            No Records found!
                        <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Are you sure?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
                It will delete assigned exam to class.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <input type="hidden" name="exam_id">
                    <input type="hidden" name="class_id">
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var exam_id = button.data('exam-id')
        var class_id = button.data('class-id')
        var modal = $(this)
        modal.find('input[name="exam_id"]').val(exam_id)
        modal.find('input[name="class_id"]').val(class_id)
    })
</script>

<?php require_once('layouts/end.php') ?>