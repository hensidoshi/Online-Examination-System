<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedTeacher()) {
    header('location:../index.php');
} else {
    $teacher = loggedTeacher();
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
                <h1 class="mt-3 h5"> <span class="badge badge-pill badge-primary"> My Profile </span> </h1>

                <div class="card mb-4 mt-3">
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
                                <div>Name: <?php echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Mobile: <?php echo htmlspecialchars($teacher['mobile'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Username: <?php echo htmlspecialchars($teacher['username'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<?php require_once('layouts/end.php') ?>