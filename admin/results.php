<?php require_once('../includes/functions.php') ?>

<?php
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['exam_id'])){
        makeLive($_POST['exam_id']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Results</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Created Exams
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $lists = getAssignedExamsAdmin();
                            if($lists){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Class Name</th>                                       
                                            <th>Date</th>
                                            <th>Results</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($lists as $list) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($list['exam_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($list['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>                                       
                                            <td><?php echo htmlspecialchars($list['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <a href="results_class.php?exam=<?php echo $list['exam_id'] ?>&&class=<?php echo $list['class_id'] ?>" class="mx-1">view</a>
                                            </td>
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

