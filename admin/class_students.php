<?php require_once('../includes/functions.php') ?>

<?php
    if(empty($_GET['class'])){
        header('location:index.php');
    } else{
        $class_id = $_GET['class'];
        if(isDataExists('classes', 'id', $class_id)){
            $class = getClass($class_id);
        } else{
            header('location:index.php');
        }
    }
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['submit'])){
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
                <h1 class="mt-3 h5"> <span class="badge badge-pill badge-primary"><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?> </span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Students
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $students = classStudents($class_id);
                            if($students){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Roll No</th>                                         
                                            <th>Username</th>
                                            <th>Profile</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($students as $student) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($student['roll_no'], ENT_QUOTES, 'UTF-8'); ?></td>                                         
                                            <td><?php echo htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><a href="student_profile.php?student=<?php echo $student['id'] ?>">Visit</a></td>
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

