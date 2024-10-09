<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addClass();
}

if (isset($_POST['update'])) {
    editClass();
}

if (isset($_POST['delete']) && isset($_POST['class_id'])) {
    deleteClass($_POST['class_id']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Class</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Avaliable Classes
                            </div>
                            <div class="col-md-3 offset-md-3">
                                <form action="" method="post">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" type="text" name="class" placeholder="Add class...">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" name="submit" type="submit"><i class="fas fa-paper-plane"></i> Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $classes = allClasses();
                        if ($classes) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Total Students</th>
                                            <th>Modify</th>
                                            <th>View Students</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($classes as $class) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $class['total_student'] ?></td>
                                                <td>
                                                    <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-class-id="<?php echo $class['id'] ?>"></i>
                                                    <i class="fas fa-edit mx-1 hover-pointer" data-toggle="modal" data-target="#editModal" data-class-id="<?php echo $class['id'] ?>" data-class-name="<?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    </i>
                                                </td>
                                                <td><a href="class_students.php?class=<?php echo $class['id'] ?>">Click Here</a></td>
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
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Are you sure?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
                It will delete the entire class with all associated data permanently.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="" method="post">
                    <input type="hidden" name="class_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
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
                <h5 class="modal-title" id="staticBackdropLabel">Edit Exam</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="class_id">
                    <div class="form-group">
                        <label for="" class="small">Class Name</label>
                        <input type="text" name="class_name" class="form-control" placeholder="Class name">
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var class_id = button.data('class-id')
        var modal = $(this)
        modal.find('input[name="class_id"]').val(class_id)
    })

    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('input[name="class_id"]').val(button.data('class-id'))
        modal.find('input[name="class_name"]').val(button.data('class-name'))
    })
</script>

<?php require_once('layouts/end.php') ?>