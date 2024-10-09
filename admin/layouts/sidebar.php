<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Core</div>
            <a class="nav-link" href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Dashboard
            </a>
            <div class="sb-sidenav-menu-heading">Users</div>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Teachers
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="add_teacher.php">Add Teacher</a>
                    <a class="nav-link" href="view_teacher.php">View Teacher</a>
                </nav>
            </div>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudent" aria-expanded="false" aria-controls="collapsePages">
                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                Students
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseStudent" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="add_student.php">Add Student</a>
                    <a class="nav-link" href="view_student.php">View Student</a>
                </nav>
            </div>
            <div class="sb-sidenav-menu-heading">Reports</div>
            <a class="nav-link" href="class.php">
                <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                Class
            </a>
            <a class="nav-link" href="exam.php">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Exams
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
            $admin = loggedAdmin();
            if($admin){
                echo htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8');
            }
        ?>
    </div>
</nav>