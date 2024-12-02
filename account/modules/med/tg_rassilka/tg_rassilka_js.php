<script>
$(document).ready(function() {
    add_modal = function() {
        $('#addModal').modal("show");
    }
    view_modal = function(id) {
        formData = new FormData();
        formData.append('item_id', id);
        js_ajax_post('tg_rassilka/tg_rassilka_view_form.php', formData).done(function(data) {
            $('#viewModal').html(data);
            $('#viewModal').modal('show');
        });
    }

    delete_modal = function (id){
      $('[name=deleteID]').val(id);
      $('#deleteModal').modal("show");
    }
});
</script>