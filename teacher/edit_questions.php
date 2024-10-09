<?php require_once('../includes/functions.php') ?>

<?php
if (!loggedTeacher()) {
    header('location:../index.php');
}
if (empty($_GET['question'])) {
    header('location:index.php');
} else {
    $question_id = $_GET['question'];
    $question = getQuestion($question_id);
    if ($question) {
        $exam_id = $question['exam_id'];
        $exam = getExam($exam_id);
        if ($exam) {
            if (!$_SESSION['user_id'] == $exam['created_teacher_id']) {
                header('location:index.php');
            }
        }
    } else {
        header('location:index.php');
    }
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['update'])) {
    updateQuestion();
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
                <h1 class="mt-3 h5">
                    <span class="badge badge-pill badge-primary">Edit Questions</span>
                </h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" name="question_id" value="<?php echo $question_id ?>">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="question" class="small">Question</label>
                                    <textarea class="form-control editor" name="question" id="question" rows="3"><?php echo isset($_POST['question']) ? htmlspecialchars($_POST['question'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="option_a" class="small">Option A</label>
                                    <textarea class="form-control editor" name="option_a" id="option_a" rows="3"><?php echo isset($_POST['option_a']) ? htmlspecialchars($_POST['option_a'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($question['option_a'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="option_b" class="small">Option B</label>
                                    <textarea class="form-control editor" name="option_b" id="option_b" rows="3"><?php echo isset($_POST['option_b']) ? htmlspecialchars($_POST['option_b'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($question['option_b'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="option_c" class="small">Option C</label>
                                    <textarea class="form-control editor" name="option_c" id="option_c" rows="3"><?php echo isset($_POST['option_c']) ? htmlspecialchars($_POST['option_c'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($question['option_c'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="option_d" class="small">Option D</label>
                                    <textarea class="form-control editor" name="option_d" id="option_d" rows="3"><?php echo isset($_POST['option_d']) ? htmlspecialchars($_POST['option_d'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($question['option_d'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="form-group col-md-3">
                                    <select class="form-control" name="correct_option" id="exampleFormControlSelect1">
                                        <option disabled selected>Select Correct Option</option>
                                        <option value="option_a" <?php echo (isset($_POST['correct_option']) && ($_POST['correct_option'] == 'option_a')) ? 'selected' : ($question['correct_option'] == 'option_a') ? 'selected' : null ?>>Option A</option>
                                        <option value="option_b" <?php echo (isset($_POST['correct_option']) && ($_POST['correct_option'] == 'option_b')) ? 'selected' : ($question['correct_option'] == 'option_b') ? 'selected' : null ?>>Option B</option>
                                        <option value="option_c" <?php echo (isset($_POST['correct_option']) && ($_POST['correct_option'] == 'option_c')) ? 'selected' : ($question['correct_option'] == 'option_c') ? 'selected' : null ?>>Option C</option>
                                        <option value="option_d" <?php echo (isset($_POST['correct_option']) && ($_POST['correct_option'] == 'option_d')) ? 'selected' : ($question['correct_option'] == 'option_d') ? 'selected' : null ?>>Option D</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                    <div class="input-group mb-3">
                                        <input type="text" name="marks" class="form-control" placeholder="Marks" value="<?php echo isset($_POST['marks']) ? $_POST['marks'] : $question['marks'] ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" name="update" type="submit" id="button-addon2"><i class="fas fa-paper-plane"></i> Update</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <a href="view_questions.php?exam=<?php echo $exam_id ?>" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col">
                                    <?php require_once('../includes/form_errors.php') ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>

    </div>
</div>

<script src="ckeditor/ckeditor.js"></script>

<script>
    $(function() {
        $('.editor').each(function(e) {
            CKEDITOR.replace(this.id, {

            });
        });
    });
</script>


<?php require_once('layouts/end.php') ?>