<script>

$(document).ready(function() {
    add_modal = function() {
        $('#addModal').modal("show");
    }
    editBanner = function(id) {
        formData = new FormData();
        formData.append('banner_id', id);
        js_ajax_post('banner_section/banner_section_edit_form.php', formData).done(function(data) {
            $('#editModal').html(data);
            $('#editModal').modal('show');
        });
    }

    deleteBanner = function (id){
      $('[name=bannerID]').val(id);
      $('#deleteModal').modal("show");
    }
});

</script>