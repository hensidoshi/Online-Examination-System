<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedTeacher()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addClass();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary"> Class </span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Avaliable Classes
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
                                            <th>View Students</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($classes as $class) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $class['total_student'] ?></td>
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<?php require_once('layouts/end.php') ?>