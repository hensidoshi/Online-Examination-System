<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (!isset($_GET['teacher'])) {
    header('location:index.php');
} else {
    $teacher = getTeacher($_GET['teacher']);
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['update'])) {
    editTeacher();
}
if (isset($_POST['changePassword'])) {
    changeTeacherPasswordByAdmin();
}
if (isset($_POST['delete']) && isset($_POST['teacher_id'])) {
    deleteTeacher($_POST['teacher_id']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary"> Profile </span></h1>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <?php
                                if ($teacher['avatar']) {
                                    $url = '../uploads/avatars/' . $teacher['avatar'];
                                } else {
                                    $url = '../includes/placeholders/150.png';
                                }
                                ?>
                                <img class="img-fluid" src="<?php echo $url; ?>">
                            </div>
                            <div class="col-md-3">
                                <div>Name: <?php echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Mobile: <?php echo htmlspecialchars($teacher['mobile'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Username: <?php echo htmlspecialchars($teacher['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">
                                    <span class="text-primary hover-pointer" data-toggle="modal" data-target="#editModal">Edit Profile</span> /
                                    <span class="text-info hover-pointer" data-toggle="modal" data-target="#changePasswordModal">Change Password</span>
                                </div>
                                <div class="mt-1"><span class="text-danger hover-pointer" data-toggle="modal" data-target="#deleteModal">Delete Teacher</span></div>
                            </div>
                            <div class="col-md-7">
                                <?php
                                $teacherExams = teacherExams($teacher['id']);
                                if ($teacherExams) {
                                ?>
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th>Exam</th>
                                            <th>Date</th>
                                            <th>is Live</th>
                                        </tr>
                                        <?php foreach ($teacherExams as $exam) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exam['exam_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($exam['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php
                                                    if ($exam['is_live']) {
                                                        echo '<i class="fas fa-circle text-success"></i>';
                                                    } else {
                                                        echo '<i class="fas fa-circle text-black-50"></i>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <?php require_once('../includes/form_errors.php') ?>
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
                It will delete the teacher & all its related data permanently.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Edit Teacher</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">Teacher Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Teacher name">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Mobile</label>
                        <input type="text" value="<?php echo $teacher['mobile'] ?>" name="mobile" class="form-control" placeholder="Mobile">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($teacher['username'], ENT_QUOTES, 'UTF-8'); ?>" name="username" class="form-control" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Avatar</label>
                        <input type="file" name="avatar" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="changePasswordModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Change Password</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="changePassword" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require_once('layouts/end.php') ?>