<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addTeacher();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Add Teacher</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name" class="small">Full name</label>
                                    <input type="text" name="name" placeholder="John Doe" class="form-control" id="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="mobile" class="small">Mobile</label>
                                    <input type="number" name="mobile" placeholder="9999988888" class="form-control" id="mobile" value="<?php echo isset($_POST['mobile']) ? $_POST['mobile'] : null ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="username" class="small">Username</label>
                                    <input type="text" name="username" placeholder="john@example.com" class="form-control" id="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password" class="small">Password</label>
                                    <input type="password" name="password" placeholder="******" class="form-control" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : null ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="avatar" class="small">Avatar</label>
                                    <input type="file" name="avatar" class="form-control-file" id="avatar">
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