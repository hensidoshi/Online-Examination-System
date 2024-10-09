<?php require_once('../includes/functions.php') ?>

<?php
if (empty($_GET['id'])) {
    header('location:index.php');
} else {
    if (!loggedStudent()) {
        header('location:../index.php');
    } else {
        $student = loggedStudent();
        $class_id = $student['class_id'];
        $exam_id = $_GET['id'];
        $questions = getQuestions($exam_id);
        $answers = getAnswers($exam_id, $_SESSION['user_id']);
        startTimer($exam_id);
        if (timerRemaining($exam_id) <= 0) {
            header('location:index.php');
        }
    }
}
if (isset($_POST['logout'])) {
    logout();
}

?>

<?php require_once('layouts/header.php') ?>

<input type="hidden" id="exam_id" value="<?php echo $exam_id ?>">
<!-- navbar starts -->
<nav class="navbar sb-topnav navbar-expand navbar-dark bg-navy">
    <a class="navbar-brand" href="index.php">E-Exam V2</a>

    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <span class="nav-link"><i class="fas fa-clock fa-fw"></i> <span id="timer">000:00</span> (Time Remaining)</span>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="results.php"><i class="fas fa-chart-bar fa-fw"></i> Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="document.getElementById('logout-form').submit();"><i class="fas fa-power-off fa-fw"></i> Logout</a>
        </li>
    </ul>

    <form action="" id="logout-form" method="post">
        <input type="hidden" name="logout">
    </form>
</nav>
<!-- navbar ends -->

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <!-- sidebar starts -->
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Questions</div>
                    <div class="px-3">
                        <?php
                        if ($questions) {
                            $count = 0;
                            foreach ($questions as $question) {
                                $answered_option = null;
                                foreach ($answers as $answer) {
                                    if ($question['id'] == $answer['question_id']) {
                                        $answered_option = $answer['answered_option'];
                                    }
                                }
                        ?>
                                <a href="<?php echo '#' . $question['id'] . '-card' ?>" class="btn btn-sm question-index mb-1 <?php echo (($answered_option != null) && ($answered_option != 'skip')) ? 'btn-success' : 'btn-light' ?>" id="<?php echo $question['id'] . '-question-index' ?>"><?php echo ++$count ?></a>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?php
                $student = loggedStudent();
                if ($student) {
                    echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8');
                }
                ?>
            </div>
        </nav>
        <!-- sidebar ends -->
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid bg-light">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($questions) {
                            $count = 0;
                            foreach ($questions as $question) {
                                $answered_option = null;
                                foreach ($answers as $answer) {
                                    if ($question['id'] == $answer['question_id']) {
                                        $answered_option = $answer['answered_option'];
                                    }
                                }
                        ?>
                                <div id="<?php echo $question['id'] . '-card' ?>" class="card mt-3 mb-4 <?php echo (($answered_option != null) && ($answered_option != 'skip')) ? 'border-success' : null ?>">
                                    <div class="card-header bg-white py-0">
                                        <div class="row">
                                            <div class="col-md-1 py-1 text-left small border-right">
                                                Question - <?php echo ++$count; ?> <br>
                                                Marks - <?php echo $question['marks'] ?>
                                            </div>
                                            <div class="col-md-11 py-1">
                                                <?php echo $question['question'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input option" type="radio" name="<?php echo $question['id'] ?>" id="<?php echo $question['id'] . '_option_a' ?>" value="option_a" <?php echo ($answered_option == 'option_a') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $question['id'] . '_option_a' ?>">Option A</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $question['option_a'] ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input option" type="radio" name="<?php echo $question['id'] ?>" id="<?php echo $question['id'] . '_option_b' ?>" value="option_b" <?php echo ($answered_option == 'option_b') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $question['id'] . '_option_b' ?>">Option B</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $question['option_b'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input option" type="radio" name="<?php echo $question['id'] ?>" id="<?php echo $question['id'] . '_option_c' ?>" value="option_c" <?php echo ($answered_option == 'option_c') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $question['id'] . '_option_c' ?>">Option C</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $question['option_c'] ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input option" type="radio" name="<?php echo $question['id'] ?>" id="<?php echo $question['id'] . '_option_d' ?>" value="option_d" <?php echo ($answered_option == 'option_d') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $question['id'] . '_option_d' ?>">Option D</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $question['option_d'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input option" type="radio" name="<?php echo $question['id'] ?>" id="<?php echo $question['id'] . '_skip' ?>" value="skip" <?php echo ($answered_option == 'skip') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $question['id'] . '_skip' ?>">Skip this question</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<script src="../sbadmin/js/polyfill.min.js"></script>
<script id="MathJax-script" async src="../sbadmin/js/tex-chtml.js"></script>


<script>
    $(document).ready(function() {
        $('.option').click(function() {
            var value = $(this).val();
            var questionId = $(this).attr('name');

            $.post("ajax_answer.php", {
                question_id: questionId,
                selected_option: value
            }).done(function(data, textStatus, jqXHR) {
                var response = JSON.parse(data);
                if (response.question_id && response.selected_option) {
                    if (response.selected_option != 'skip') {
                        $('#' + response.question_id + '-question-index').removeClass('btn-light').addClass('btn-success');
                        $('#' + response.question_id + '-card').addClass('border-success');
                    } else {
                        $('#' + response.question_id + '-question-index').removeClass('btn-success').addClass('btn-light');
                        $('#' + response.question_id + '-card').removeClass('border-success');
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            });
        });

        //Timer
        var examId = $('#exam_id').val();
        var interval = setInterval(timer, 1000);

        function timer() {
            $.post("ajax_timer.php", {
                exam_id: examId
            }).done(function(data, textStatus, jqXHR) {
                var response = JSON.parse(data);
                if (response.remaining <= 0) {
                    clearInterval(interval);
                    $('#timer').text('000:00');
                    Swal.fire({
                        title: 'Time Over!',
                        text: 'Redirecting to dashboard in 5 seconds...',
                        icon: 'warning',
                        timer: 5000,
                        confirmButtonText: 'Dashboard',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    }).then(function() {
                        window.location = "index.php";
                    })
                } else {
                    var remaining = showTimer(response.remaining);
                    console.log(remaining);
                    $('#timer').text(remaining);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            });
        }

        function showTimer(time) {
            var minutes = Math.floor(time / 60);
            var seconds = time - minutes * 60;
            var finalTime = str_pad_left(minutes, '0', 3) + ':' + str_pad_left(seconds, '0', 2);
            return finalTime;
        }

        function str_pad_left(string, pad, length) {
            return (new Array(length + 1).join(pad) + string).slice(-length);
        }
    })
</script>

<?php require_once('layouts/end.php') ?>