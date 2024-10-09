<?php
if (isset($_POST['changePassword'])) {
    changeMyPassword('admins');
}
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-navy">
    <a class="navbar-brand" href="index.php">E-Exam V2</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>

    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="dropdown">
            <div class="input-group">
                <input type="text" class="form-control " name="student_search" id="student_search" placeholder="Search students..." autocomplete="off">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-info "><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-lg bg-light" id="search_result" style="width:100%!important;"></div>
        </div>
    </form>

    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i> Account</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
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