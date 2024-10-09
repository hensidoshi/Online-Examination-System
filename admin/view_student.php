<?php require_once('../includes/functions.php') ?>

<?php
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['submit'])){
        addTeacher();
    }
    if(isset($_POST['delete']) && isset($_POST['student_id'])){
        deleteStudent($_POST['student_id']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Students</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Recenty Registered Students
                            </div>
                            <div class="col-md-3 offset-md-3">
                                <form action="class_students.php" method="get">
                                    <div class="input-group input-group-sm">
                                        <select class="form-control" name="class">
                                            <option disabled selected>select</option>
                                            <?php 
                                                $classes = allClasses();
                                                if($classes){
                                                    foreach($classes as $class){
                                                        $selected = null;
                                                        if(isset($_POST['class_id'])){
                                                            if($_POST['class_id']==$class['id']){
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        echo "<option value='{$class['id']}' {$selected}>".htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8')."</option>";
                                                    }
                                                    
                                                }
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-eye"></i> View</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $students = recentStudents();
                            if($students){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Roll No</th>
                                            <th>Class</th>                                           
                                            <th>Username</th>
                                            <th>Delete</th>
                                            <th>Profile</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($students as $student) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($student['roll_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($student['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>                                           
                                            <td><?php echo htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-student-id="<?php echo $student['id'] ?>"></i>
                                            </td>
                                            
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

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Are you sure?</h5>
        <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
      </div>
      <div class="modal-body">
        It will delete the student & all its related data permanently.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <form action="" method="post">
            <input type="hidden" name="student_id">
            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var student_id = button.data('student-id')
        var modal = $(this)
        modal.find('input[name="student_id"]').val(student_id)
    })
</script>

<?php require_once('layouts/end.php') ?>

