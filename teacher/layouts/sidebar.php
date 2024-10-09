<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link" href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>

            <div class="sb-sidenav-menu-heading">Reports</div>
            <a class="nav-link" href="view_student.php">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                Students
            </a>


            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExams" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Exams
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseExams" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="create_exam.php">Create Exam</a>
                    <a class="nav-link" href="view_exam.php">View Exams</a>
                    <a class="nav-link" href="assign_exam.php">Assign Exams</a>
                </nav>
            </div>

            <a class="nav-link" href="class.php">
                <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                Class
            </a>



            <a class="nav-link" href="results.php">
                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                Results
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        <?php
        $teacher = loggedTeacher();
        if ($teacher) {
            echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8');
        }
        ?>
    </div>
</nav>