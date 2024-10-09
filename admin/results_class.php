<?php require_once('../includes/functions.php') ?>

<?php
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(empty($_GET['exam']) || empty($_GET['class'])){
        header('location:index.php');
    } else{
        $exam_id = $_GET['exam'];
        $class_id = $_GET['class'];
        $exam = getExam($exam_id);
        $results = results($class_id, $exam_id);
    }
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>


<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php require_once('layouts/sidebar.php') ?>
    </div>
    <div id="layoutSidenav_content">
        <div class="container-fluid">
            <h1 class="mt-3 h5">
                <span class="badge badge-pill badge-primary"> Results</span>
                <span class="text-primary h5">
                    <?php
                        echo htmlspecialchars($exam['exam_name'], ENT_QUOTES, 'UTF-8');
                    ?>
                </span>
            </h1>

            <?php 
                $marksArray = array();
                $total_marks = $exam['total_marks'];
                $highest = 0;
                $average = 0;
                $lowest = 0;
                if($results){
                    $lowest = $total_marks;
                    foreach($results as $result){
                        $marks = $result['obtain'];
                        array_push($marksArray, $marks);
                        if($marks >= $highest){
                            $highest = $marks;
                        }
                        if($marks <= $lowest){
                            $lowest = $marks;
                        }
                    }
                }
                if(count($marksArray) > 0){
                    $average = array_sum($marksArray)/count($marksArray);
                }
            ?>
            
            <div class="card mt-3 mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <i class="fas fa-table mr-1"></i>
                            <?php 
                                $class = getClass($class_id);
                                echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8');
                            ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="text-info hover-pointer" id="printBtn" onclick="window.print();">Print Result</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr class="bg-light">
                            <td>Total Marks:  <?php echo $total_marks ?></td>
                            <td>Pass Marks:  <?php echo $exam['pass_marks'] ?></td>
                            <td>Highest Score: <?php echo $highest ?></td>
                            <td>Lowest Score: <?php echo $lowest ?></td>
                            <td>Average: <?php echo round($average, 2) ?></td>
                        </tr>
                    </table>

                    <?php
                        if($results){
                    ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="resultTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Name</th>
                                        <th>Roll No</th>                                         
                                        <th>Marks Obtain</th>
                                        <th>Result</th>
                                        <th>Profile</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach($results as $result) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($result['roll_no'], ENT_QUOTES, 'UTF-8'); ?></td>                                         
                                        <td><?php echo htmlspecialchars($result['obtain'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php  
                                                if($result['obtain'] >= $exam['pass_marks']){
                                                    echo '<span class="text-success">Passed</span>';
                                                } else{
                                                    echo '<span class="text-danger">Failed</span>';
                                                }
                                            ?>
                                        </td>
                                        <td><a href="student_profile.php?student=<?php echo $result['student_id'] ?>">Visit</a></td>
                                    </tr>   
                                    <?php } ?>                                        
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<?php require_once('layouts/end.php') ?>

