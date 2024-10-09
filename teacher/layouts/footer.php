<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; E-Exam 2020</div>
            <div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>
<script src="../sbadmin/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //Student search suggestions
        $('#student_search').on('keyup', function() {
            $('#search_result').dropdown('hide');
            $('#search_result').html('');
            var search_value = $(this).val();
            console.log(search_value);
            $.ajax({
                type: 'POST',
                url: '../includes/ajax_student_search.php',
                data: {
                    search_value: search_value
                },
                success: function(result) {
                    if (result.length > 0) {
                        $('#search_result').dropdown('show');
                        $('#search_result').html(result);
                        $('.search-result-item').on('click', function() {
                            var href = $(this).attr('href');
                            window.open(href, '_blank');
                        });
                    } else {
                        $('#search_result').dropdown('hide');
                    }
                }
            });
        });
    });
</script>