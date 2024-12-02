<script>

taskComplete = function (id){
  $('[name=taskCompleteID]').val(id);
  $('#taskCompleteModal').modal("show");
}

document.addEventListener("DOMContentLoaded", function() {
  const selectElement = document.getElementById("click_type");
  const getGroup = document.getElementById("click_group");
  const getStudent = document.getElementById("click_student");

  selectElement.addEventListener("change", function(event) {

    const selectedValue = event.target.value;
    
    if (selectedValue === "group") {

      getGroup.style.display = "block";
      getStudent.style.display = "none"; 
      
    } else if(selectedValue === "student"){

      getStudent.style.display = "block";
      getGroup.style.display = "none";

    } else {

      getGroup.style.display = "none";
      getStudent.style.display = "none"; 
      
    }
  });
});

</script>