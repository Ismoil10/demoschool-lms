<script>

$(document).ready(function() {
   
    add_modal = function(){
        $('#addModal').modal('show');
    }

    checkHomework = function(id) {
        console.log(id);
        formData = new FormData();
        formData.append('homework_id', id);
        js_ajax_post('student_homework/student_homework_check.php', formData).done(function(data) {
            $('#checkModal').html(data);
            $('#checkModal').modal('show');
        });
    }

    rejectHomework = function(id) {
        console.log(id);
        formData = new FormData();
        formData.append('homework_id', id);
        js_ajax_post('student_homework/student_homework_reject.php', formData).done(function(data) {
            $('#rejectModal').html(data);
            $('#rejectModal').modal('show');
        });
    }

});

</script>