<script>

$(document).ready(function() {
    add_modal = function() {
        $('#addModal').modal("show");
    }
    editStudent = function(id) {
        formData = new FormData();
        formData.append('student_id', id);
        js_ajax_post('course_students/course_students_edit_form.php', formData).done(function(data) {
            $('#editModal').html(data);
            $('#editModal').modal('show');
        });
    }

    deleteStudent = function (id){
      $('[name=studentID]').val(id);
      $('#deleteModal').modal("show");
    }
});

</script>