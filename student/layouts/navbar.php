<?php
if (isset($_POST['changePassword'])) {
    changeMyPassword('students');
}
?>

<nav class="navbar sb-topnav navbar-expand navbar-dark bg-navy">
    <a class="navbar-brand" href="index.php">E-Exam V2</a>

    <!-- Navbar-->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="results.php"><i class="fas fa-chart-bar fa-fw"></i> Results</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i> Account</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="myprofile.php"><i class="fas fa-user fa-fw"></i> My Profile</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changeMyPasswordModal"><i class="fas fa-unlock-alt fa-fw"></i> Change Password</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit();"><i class="fas fa-power-off fa-fw"></i> Logout</a>
            </div>
        </li>
    </ul>

    <form action="" id="logout-form" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
        <input type="hidden" name="logout">
    </form>
</nav>


<!-- Modal -->
<div class="modal fade" id="changeMyPasswordModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Change Password</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="small">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Current Password">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="changePassword" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>