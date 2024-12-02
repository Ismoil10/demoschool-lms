<script>
  async function showDetails(e) {
    const {
      from,
      to,
      teacher,
      exception
    } = e.target.dataset;
    const modal = new bootstrap.Modal(document.getElementById("singleTeacherModal"));
    if (teacher) {
      const option = {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
          from,
          to,
          teacher,
          exception
        })
      };
      const res = await fetch("/account/ajax/report/single_teacher_report.php", option);
      const resData = await res.text();
      const modalFram = document.querySelector(".modal-report-body");
      modalFram.innerHTML = resData;
      modal.show();
      modalFram.querySelector("#recalculate").addEventListener("click", updatePrice);
    }
  }
  function updatePrice(){
    const inputField = document.querySelector(".modal-report-body input#teacher-salary");
    const alert = document.querySelector(".modal-report-body #alert-box");
    const alertMessage = document.querySelector(".modal-report-body #alert-message");
    const rows = document.querySelectorAll(".modal-report-body table tbody tr");
    const totalCol = document.querySelector(".modal-report-body table tfoot td[data-column=total]");
    if(inputField && rows.length > 0){
      let totalSalar = 0;
      const salary = +inputField.value.replace(/\D/g,"");
      rows.forEach((elem) => {
        const planed = +elem.querySelector("td[data-column=planed]").textContent;
        const actual = +elem.querySelector("td[data-column=actual]").textContent;
        const salaryCol = elem.querySelector("td[data-column=salary]");
        calculatedSalary = (salary/ planed) * actual;
        totalSalar += calculatedSalary;
        salaryCol.textContent = Math.round(calculatedSalary).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
      });
      totalCol.innerHTML = `<b>Jami: </b>${Math.round(totalSalar).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")}`;
    }else{
      alert.classList.remove("d-none");
      alertMessage.textContent = "Xato: maʼlumotlar topilmadi";
      setTimeout(() => {
        alert.classList.add("d-none");
        alertMessage.textContent = "";
      }, 2000);
    }

  }
  const tbody = document.querySelector(".salaryReport tbody");
  tbody.addEventListener("click", showDetails);

  function addExDate(e) {
    const getRange = document.querySelector(".get-range");
    const div = document.createElement("div");
    const inputBox = document.querySelector(".exception-dates");
    const addBtn = document.querySelector(".add-ex-button");

    const dateArr = getRange.value.trim().split("to");
    div.className = "mb-3";
    div.innerHTML = `<label>Istisno sanalar</label>
    <input type="date" name="exception_date[]" class="form-control" min="${dateArr[0].trim()}" max="${dateArr[1].trim()}">`;
    inputBox.insertBefore(div, addBtn);
  }

  function getRange(e) {
    const reportType = document.getElementById("report-type");
    const inputBox = document.querySelector(".exception-dates");
    if (e.value.length >= 24 && reportType.value == "salaryReport") {
      const dateArr = e.value.trim().split("to");
      inputBox.classList.remove("d-none");
      const button = document.createElement("button");
      button.type = "button";
      button.className = "btn btn-outline-primary btn-icon rounded-circle waves-effect add-ex-button mb-1";
      button.innerHTML = feather.icons["plus"].toSvg();
      button.addEventListener("click", addExDate);
      inputBox.innerHTML = `<div class="mb-3">
        <label>Istisno sanalar</label>
        <input type="date" name="exception_date[]" class="form-control" min="${dateArr[0].trim()}" max="${dateArr[1].trim()}">
      </div>`;
      inputBox.appendChild(button);
    }
  }
</script>