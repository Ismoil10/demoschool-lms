<script>
  $(document).ready(function() {
    add_modal = function() {
      $('#addModal').modal("show");
    }

    function sendFile(files, editor, welEditable) {
      formData = new FormData();
      formData.append("img_file", files);
      js_ajax_post('student_courses/add_image.php', formData).done(function(data) {
        console.log(data);
      });
    }

    editLesson = function(id) {
      formData = new FormData();
      formData.append('item_id', id);
      js_ajax_post('student_courses/student_modules_edit_form.php', formData).done(function(data) {
        console.log(data);
        $('#editLesson').html(data);
        $('#editLesson').modal('show');
      });
    }

    add_lesson = function() {
      $('#addLesson').modal("show");
    }

    add_module = function() {
      $('#addModalModule').modal("show");
    }

    editModule = function(id) {
      formData = new FormData();
      formData.append('item_id', id);
      js_ajax_post('student_courses/student_sections_edit_form.php', formData).done(function(data) {
        console.log(data);
        $('#editModule').html(data);
        $('#editModule').modal('show');
      });
    }

    editQuestion = function(id) {
      formData = new FormData();
      formData.append('item_id', id);
      js_ajax_post('student_courses/student_questions_edit_form.php', formData).done(function(data) {
        $('#editModal').html(data);
        $('#editModal').modal('show');
        setTimeout(() => $('.edit-summernote').summernote({
          toolbar: [
            ['style', ['style']],
            ['fontsize', ['fontsize']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'hr']],
            ['table', ['table']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ],
          height: 200,
          minHeight: null,
          maxHeight: 200,
          focus: true,
          onEditImageUpload: function(files, editor, welEditable) {
            sendEditFile(files[0], editor, welEditable);
          }
        }), 1000);
      });
    }
    editCourse = function(id) {
      formData = new FormData();
      formData.append('item_id', id);
      js_ajax_post('student_courses/student_courses_edit_form.php', formData).done(function(data) {
        $('#editCourse').html(data);
        $('#editCourse').modal('show');
      });
    }

    deleteQuestion = function(id) {
      $('[name=deleteID]').val(id);
      $('#deleteModal').modal("show");
    }

    deleteLesson = function(id) {
      $('[name=lessonID]').val(id);
      $('#deleteLesson').modal("show");
    }

    deleteModule = function(id) {
      $('[name=moduleID]').val(id);
      $('#deleteModule').modal("show");
    }

    deleteCourse = function(id) {
      $('[name=courseID]').val(id);
      $('#deleteCourse').modal("show");
    }
  });

  $("#selectID").on("change", function(e) {
    GetInfo = $(".modal-dialog #click_info");
    GetVideo = $(".modal-dialog #click_video");
    GetFile = $(".modal-dialog #click_file");
    GetDrag = $(".modal-dialog #click_drag");
    GetInput = $(".modal-dialog #input_file");
    GetAttr = $(".try-file").prop("required", false);
    addTryF = $("#tryF");
    addTryD = $("#tryD");
    addTryP = $("#tryP");
    GetInfo.hide();
    GetVideo.hide();
    GetFile.hide();
    GetDrag.hide();
    GetInput.hide();
    switch (e.target.value) {
      case "info":
        GetInfo.show();
        $(".addButton").attr("id", "infoButton");
        $(".addButton").attr("type", "submit");
        $(".addButton").attr("onclick", "checkInfoInput()");
        break;
      case "video":
        GetVideo.show();
        $(".addButton").attr("id", "videoButton");
        $(".addButton").attr("type", "submit");
        $(".addButton").attr("onclick", "checkVideoInput()");
        break;
      case "selected-file":
        GetFile.show();
        addTryF.prop("required", true);
        addTryD.prop("required", false);
        addTryP.prop("required", false);
        $(".addButton").attr("id", "fileButton");
        $(".addButton").attr("onclick", "checkFileInput()");
        break;
      case "selected-drag":
        GetDrag.show();
        addTryD.prop("required", true);
        addTryF.prop("required", false);
        addTryP.prop("required", false);
        $(".addButton").attr("id", "dragButton");
        $(".addButton").attr("onclick", "checkDragInput()");
        break;
      case "input-file":
        GetInput.show();
        addTryP.prop("required", true);
        addTryD.prop("required", false);
        addTryF.prop("required", false);
        $(".addButton").attr("id", "inputButton");
        $(".addButton").attr("onclick", "checkInput()");
        break;
      default:
        break;
    }
  });
</script>