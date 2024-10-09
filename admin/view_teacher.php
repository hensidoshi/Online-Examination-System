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
    if(isset($_POST['update'])){
        editTeacher();
    }
    if(isset($_POST['delete']) && isset($_POST['teacher_id'])){
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Teachers</h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <i class="fas fa-table mr-1"></i>
                        Teacher Table
                    </div>
                    <div class="card-body">
                        <?php
                            $teachers = allTeachers();
                            if($teachers){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Username</th>
                                            <th>Modify</th>
                                            <th>Profile</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($teachers as $teacher) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($teacher['mobile'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($teacher['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-teacher-id="<?php echo $teacher['id'] ?>"></i>
                                            </td>
                                            
                                            <td><a href="teacher_profile.php?teacher=<?php echo $teacher['id'] ?>">Visit</a></td>
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
        It will delete the teacher permanently.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <form action="" method="post">
            <input type="hidden" name="teacher_id">
            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var teacher_id = button.data('teacher-id')
        var modal = $(this)
        modal.find('input[name="teacher_id"]').val(teacher_id)
    })
</script>

<?php require_once('layouts/end.php') ?>

