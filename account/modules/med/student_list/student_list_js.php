<script>
  async function getStudentInfo(id) {
    const options = {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `student=${id}`
    };
    const res = await fetch("/account/ajax/group_list/get_student_info.php", options);
    const resData = await res.json();
    return resData;
  }

  function paymentModal(id) {
    const student = id;
    const studentNameField = document.querySelector(".info-box #studentName");
    const balanceField = document.querySelector(".info-box #studentBallance");
    getStudentInfo(student).then(data => {
      studentNameField.innerHTML = data.student;
      balanceField.innerHTML = data.ballance;
      $("[name=singleStudentPaymentId]").val(student);
      $("#singleStudentPaymentModal").modal("show");
    }).catch(err => {
      alert("Error");
      console.log(err);
    });
  }

  function deleteModal(id) {
    $("[name=deleteId]").val(id);
    $("#deleteModal").modal("show");
  }

  function reminder(id) {
    $("[name=reminderUserId]").val(id);
    $("#reminderModal").modal("show");
  }

  function showText(e) {
    e.classList.toggle("single-line");
  }

  function studentFileFunction(id) {
    $("#studentFileModal input[name=student_id]").val(id);
    $("#studentFileModal").modal("show");
  }

  function editTransaction(jsonData) {
    const data = JSON.parse(JSON.parse(jsonData));
    const validDate = data.CHANGED_DATE.split(' ')[0];

    $("[name=editPaymentAmount]").val(data.AMOUNT);
    $("[name=editPaymentDate]").val(validDate);
    $(`.editPaymentCheckbox [value=${data.TYPE}]`).prop("checked", true);
    $("[name=editTransactionId]").val(data.ID);
    $("#editTransactionModal").modal("show");
  }

  function removeTranModal(id) {
    $("[name=removeTranStudentID]").val(id);
    $("#removeTranModal").modal("show");
  }

  function editModal(elem) {
    data = JSON.parse(elem.dataset.json);
    if (data.ESHITGAN_JOYI) {
      eshitgan = JSON.parse(data.ESHITGAN_JOYI);
    } else {
      eshitgan = {
        input: "",
        select: ""
      }
    }

    function isJsonString(str) {
      try {
        JSON.parse(str);
        return true;
      } catch (e) {
        return false;
      }
    }

    $("[name=edit_name]").val(data.NAME);
    $("[name=edit_phone]").val(data.PHONE);
    $("[name=edit_email]").val(data.EMAIL);
    $("[name=edit_language]").val(data.LANG);
    if (isJsonString(data.PARENT_PHONE)) {
      $("[name=edit_parent_phone]").val(data.PARENT_PHONE);
      $("[name=edit_parent_phone]").prop("readonly", true);
    } else {
      $("[name=edit_parent_phone]").val(data.PARENT_PHONE);
      $("[name=edit_parent_phone]").prop("readonly", false);
    }
    $("[name=edit_soho_id]").val(data.SOHO_ID ?? "");
    $("[name=edit_tg_username]").val(data.TG_USERNAME);
    $("[name=edit_parent_name]").val(data.PARENT_NAME);
    $("[name=edit_birth_date]").val(data.BIRTH_DATE);
    $("[name=edit_address]").val(data.ADDRESS);
    $("[name=edit_test_result]").val(data.TEST_SCORE);
    if (!eshitgan.select) {
      $("[name=\"edit_came_from[input]\"]").val('');
      $("[name=\"edit_came_from[select]\"]").val('');
      $(".edit_came_from_input").addClass("d-none");
    } else if (eshitgan.select == "call_center") {
      $("[name=\"edit_came_from[select]\"]").val(eshitgan.select);
      $("[name=\"edit_came_from[input]\"]").val(eshitgan.input);
      $(".edit_came_from_input").removeClass("d-none");
    } else {
      $("[name=\"edit_came_from[input]\"]").val('');
      $("[name=\"edit_came_from[select]\"]").val(eshitgan.select);
      $(".edit_came_from_input").addClass("d-none");
    }
    $("[name=editId]").val(data.ID);
    $("#editModal").modal("show");
  }

  function coinModal(id, debt) {
    $("[name=coinStudentID]").val(id);
    const radioInput = document.querySelector("#coinModal input#add");
    const alertBody = document.querySelector("#coinModal .alert-body");
    const roleId = +document.querySelector("#coinModal").dataset.roleId;
    if (debt == "block") {
      radioInput.disabled = roleId == 1 ? false : true;
      alertBody.parentElement.classList.remove("d-none");
      alertBody.textContent = roleId == 1 ?
        "Talabaning balansi talab qilinadigan limitga to'g'ri kelmaydi" :
        "Talabaning balansi kutilgan diapazondan tashqarida.\n Harakatingizni davom ettirish uchun toʻlovni amalga oshiring.";
      setTimeout(() => alertBody.parentElement.classList.add("d-none"), 3500);
    } else {
      alertBody.parentElement.classList.add("d-none");
      alertBody.textContent = "";
      radioInput.disabled = false;
    }
    $("#coinModal").modal("show");
  }

  function changeSelect(e) {
    const select = document.querySelector("select[name=studentDeleteReason]");
    if (this.value === "demoClass") {
      select.innerHTML = `<option value="Ustoz yoqmadi">Ustoz yoqmadi</option>
        <option value="Kurs qimmat">Kurs qimmat</option>
        <option value="Manzil uzoq">Manzil uzoq</option>
        <option value="Darsga qiziqmadi">Darsga qiziqmadi</option>
        <option value="Tel. o'chiq">Tel. o'chiq</option>
        <option value="Dubl">Dubl</option>
        <option value="Rus tilida kerak">Rus tilida kerak</option>
        <option value="Vaqti yo'q">Vaqti yo'q</option>
        <option value="Boshqa o'quv markaziga keti">Boshqa o'quv markaziga keti</option>
        <option value="Darsga umuman kelmagan">Darsga umuman kelmagan</option>`;
    } else {
      select.innerHTML = `<option value="Ustoz yoqmadi">Ustoz yoqmadi</option>
        <option value="Kurs qimmat">Kurs qimmat</option>
        <option value="Manzil uzoq">Manzil uzoq</option>
        <option value="Darsga qiziqmadi">Darsga qiziqmadi</option>
        <option value="Vaqti yo'q">Vaqti yo'q</option>
        <option value="Boshqa o'quv markaziga keti">Boshqa o'quv markaziga keti</option>
        <option value="Bitirdi">Bitirdi</option>`;
    }
  }

  function specialPrice() {
    $("#specialPriceModal").modal("show");
  }
  try {
    document.querySelector(".came_from_select").addEventListener("input", (e) => {
      if (e.target.value == "call_center") {
        document.querySelector(".came_from_input").classList.remove("d-none");
      } else {
        document.querySelector(".came_from_input").classList.add("d-none");
      }
    });
  } catch (error) {
    console.log(error);
  }

  try {
    document.querySelector(".edit_came_from_select").addEventListener("input", (e) => {
      if (e.target.value == "call_center") {
        document.querySelector(".edit_came_from_input").classList.remove("d-none");
      } else {
        document.querySelector(".edit_came_from_input").classList.add("d-none");
      }
    });
  } catch (error) {
    console.log(error);
  }
  document.querySelector(".group-list-select").addEventListener("input", function(e) {
    const {
      price,
      laptop,
      type
    } = e.target.options[e.target.selectedIndex].dataset;
    const priceInput = document.querySelector(".special-price-input");
    const giveLaptop = document.querySelector("#give-laptop");
    const notGiveLaptop = document.querySelector("#not-give-laptop");
    const forMonthlySubscription = document.getElementById("for-monthly-subscription");
    const forSimpleSubscription = document.getElementById("for-simple-subscription");

    if (type === "monthly") {
      forMonthlySubscription.classList.remove("d-none");
      forSimpleSubscription.classList.add("d-none");

      forMonthlySubscription.lastElementChild.required = true;
      forSimpleSubscription.lastElementChild.required = false;
    } else {
      forMonthlySubscription.classList.add("d-none");
      forSimpleSubscription.classList.remove("d-none");

      forMonthlySubscription.lastElementChild.required = false;
      forSimpleSubscription.lastElementChild.required = true;
    }

    if (laptop == "true") {
      giveLaptop.checked = true;
      notGiveLaptop.checked = false;
    } else {
      giveLaptop.checked = false;
      notGiveLaptop.checked = true;
    }
    if (price) {
      priceInput.removeAttribute("readonly");
      priceInput.value = parseInt(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    } else {
      priceInput.setAttribute("readonly");
      priceInput.value = "";
    }
  });
  try {
    document.querySelector("#customRadio1").addEventListener("click", changeSelect);
    document.querySelector("#customRadio2").addEventListener("click", changeSelect);
    document.querySelector(".inputAge").addEventListener("input", function(e) {
      const regEx = /^\d{4}-\d{2}-\d{2}$/;
      if (!e.target.value.match(regEx)) return false;
      // Get the current date.
      var currentDate = new Date();
      const birthDate = new Date(e.target.value);
      // Calculate the difference between the current date and the date of birth.
      var difference = currentDate - birthDate;
      // Get the number of years in the difference.
      var years = Math.floor(difference / (1000 * 60 * 60 * 24 * 365.25));

      e.target.nextElementSibling.firstElementChild.textContent = `Yoshi: ${years}`;
    });

  } catch (error) {
    console.log(error);
  }

  function smsModal() {
    $("#smsModal").modal("show");
  }

  addNewStudent = function() {
    $('#onlineCourseModal').modal("show");
  }

  function insertMessage(e) {
    if (e.target.classList.contains("message")) {
      const textArea = document.querySelector(".message-textarea");
      const messageText = e.target.textContent;
      textArea.value = messageText.trim().replace(/(\r\n|\n|\r)/gm, "");
    }
  }
  document.querySelector(".message-templates").addEventListener("click", insertMessage);

  function conformModal(id, type) {
    let questionText = document.querySelector("#conformModal .question-text");
    let modalTitle = document.querySelector("#conformModal .modal-title");
    let actionId = document.querySelector("#conformModal input[name=actionId]");
    let submitBtn = document.querySelector("#conformModal .conform-action");
    switch (type) {
      case "resendSubmit":
        modalTitle.innerHTML = "SMS Qayta jo'natish";
        questionText.innerHTML = "Ushbu SMS'ni qaytadan jo'natmoqchimisiz?";
        submitBtn.setAttribute("name", "resendSubmit");
        break;
      case "removeFromGroup":
        modalTitle.innerHTML = "Gruhdan O'chirish";
        questionText.innerHTML = "Ushbu talabani gruhdan o'chirmoqchimisiz?";
        submitBtn.setAttribute("name", "removeFromGroupSubmit");
        break;
      case "removeTransaction":
        modalTitle.innerHTML = "Tranzaksiya O'chirish";
        questionText.innerHTML = "Ushbu Tranzaksiyani o'chirmoqchimisiz?";
        submitBtn.setAttribute("name", "removeTransactionSubmit");
        break;
      case "removeGroup":
        modalTitle.innerHTML = "Guruhni O'chirish";
        questionText.innerHTML = "Ushbu Guruhni o'chirmoqchimisiz?";
        submitBtn.setAttribute("name", "removeGroupSubmit");
        break;
      case "deleteFile":
        modalTitle.innerHTML = "Faylni O'chirish";
        questionText.innerHTML = "Ushbu Faylni o'chirmoqchimisiz?";
        submitBtn.setAttribute("name", "deleteFileSubmit");
        break;
      case "closeTask":
        modalTitle.innerHTML = "Vazifani yopish";
        questionText.innerHTML = `Bu vazifani yopmoqchimisiz? <br><br><br> <label>Izoh</label>
        <textarea class="form-control" form="resendForm" name="comment" minlength="10" required></textarea>`;
        submitBtn.setAttribute("name", "closeTaskSubmit");
        break;
      case "activateSubscribe":
        modalTitle.innerHTML = "Foallashtirish";
        questionText.innerHTML = `Bu talabani faollashtirmoqchimisiz? <br><br><br> 
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="activate_only_lead"  form="resendForm" class="custom-control-input" id="activate-only-lead" value="lead_only" checked />
          <label class="custom-control-label" for="activate-only-lead">Faqat Leadni</label>
        </div>
        `;
        submitBtn.setAttribute("name", "activateSubscribeSubmit");
        break;
      default:
        alert("Error 500 Not found");
        break;
    }
    actionId.value = id;
    $("#conformModal").modal("show");
  }

  function deactiveSubModal(subId) {
    const res = confirm("Ushbu talabani gurihdan o'chimoqchimisiz?");
    if (res) {
      alert("Error");
    }
  }

  function addPriceModal() {
    $("#addPriceModal").modal("show")
  }
</script>
<script>
  const tranTable = document.querySelectorAll("#student-trans-table tbody tr");
  let isFirst = true;
  let totalAmount = 0;
  let indexOfFirst;
  let rowAmount = 0;
  const arrowDownIcon = document.createElement("i");
  arrowDownIcon.className = "fa fa-arrow-circle-down";

  function prettyNum(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
  }
  tranTable.forEach((tr, index) => {

    if (tr.dataset.type !== "taken") {
      isFirst = true;
      totalAmount = 0;
      indexOfFirst = '';
      rowAmount = 0;
    } else if (tr.dataset.type === "taken") {
      amount = tr.children[1].firstElementChild;
      totalAmount += +amount.textContent.replaceAll(" ", "");
      rowAmount++;
      if (isFirst) {
        tr.classList.add(`fr-${index}`);
        indexOfFirst = `fr-${index}`;

        tr.dataset.curValue = amount.textContent.replaceAll(" ", "");

        tr.lastElementChild.dataset.staffName = tr.lastElementChild.textContent.trim();
        tr.lastElementChild.innerHTML = "...";

        const arrowDownIcon = document.createElement("i");
        arrowDownIcon.addEventListener("click", openRows);
        arrowDownIcon.className = "fa fa-arrow-circle-down";
        // tr.firstElementChild.appendChild(arrowDownIcon);
        tr.firstElementChild.insertAdjacentElement("afterbegin", arrowDownIcon);
      } else {
        tr.classList.add("d-none");
        document.querySelector(`.${indexOfFirst} td:nth-child(2) span`).innerText = prettyNum(totalAmount) + ` / ${rowAmount}`;
      }
      isFirst = false;
    }

  });

  function openRows(e) {
    const tr = e.target.parentNode.parentNode;
    if (e.target.classList.contains("fa-arrow-circle-down")) {
      e.target.classList.replace("fa-arrow-circle-down", "fa-arrow-circle-up");
    } else {
      e.target.classList.replace("fa-arrow-circle-up", "fa-arrow-circle-down");
    }
    // Taken value of target element befor droping down 
    let elemAmount = tr.dataset.curValue;
    // putting value of sum of taken rows in data-cur-value attribute
    tr.dataset.curValue = tr.firstElementChild.nextElementSibling.firstElementChild.textContent;
    // Putting value of an attribute 
    tr.firstElementChild.nextElementSibling.firstElementChild.textContent = prettyNum(elemAmount);
    // Taking text of staff
    let staff = tr.lastElementChild.textContent;
    // Assigning attribute data-staff-name to a textContent 
    tr.lastElementChild.textContent = tr.lastElementChild.dataset.staffName;
    // Putting old name to attribute data-staff-name
    tr.lastElementChild.dataset.staffName = staff;
    if (tr.nextElementSibling && tr.nextElementSibling.dataset.type === "taken") {
      openSiblings(tr.nextElementSibling);
    }
  }

  function openSiblings(curElem) {
    if (curElem.classList.contains("d-none")) {
      curElem.classList.remove("d-none");
    } else {
      curElem.classList.add("d-none");
    }
    if (curElem.nextElementSibling && curElem.nextElementSibling.dataset.type === "taken") {
      openSiblings(curElem.nextElementSibling);
    }
  }

  /* JQuery */
  function checkPrint(e) {
    const regex = /\B(?=(\d{3})+(?!\d))/g;
    const value = e.dataset.amount.trim().replace(/\s|[a-zA-Z]/g, '');
    const formattedValue = value.replace(regex, ' ');
    const [studentName, phone] = JSON.parse(e.dataset.student);
    $("#transaction-id").text(e.dataset.transaction);
    $("#student-name").text(studentName);
    $("#phone").text(phone);
    $("#payment-amount").text(formattedValue);
    $("#group").text(e.dataset.group);
    $("#price").text(e.dataset.price.replace(regex, " "));
    $("#filial").text($("#main-menu-navigation li.navigation-header span b").text());
    $("#teacher-name").text(e.dataset.teacher);
    $("#by-user").text(e.dataset.byUser);
    $("#date").text(e.dataset.date);

    /* INPUTS */

    $("[name=transaction_id]").val(e.dataset.transaction);
    $("[name=student_name]").val(studentName);
    $("[name=phone]").val(phone);
    $("[name=payment_amount]").val(formattedValue);
    $("[name=group]").val(e.dataset.group);
    $("[name=price]").val(e.dataset.price.replace(regex, " "));
    $("[name=filial]").val($("#main-menu-navigation li.navigation-header span b").text());
    $("[name=teacher_name]").val(e.dataset.teacher);
    $("[name=by_user]").val(e.dataset.byUser);
    $("[name=date]").val(e.dataset.date);

    $("#checkPrint").modal("show");
  }

  function editSubscription(el) {
    const {
      id,
      type,
      startDate,
      laptopStatus
    } = el.dataset;
    $("[name=edit_subscription_id]").val(id);
    $("[name=subscription_type]").val(type);
    $("[name=edit_start_date]").val(startDate);
    if (laptopStatus == "true") {
      $("#given-laptop").prop("checked", true);
      $("#not-given-laptop").prop("checked", false);
    } else {
      $("#not-given-laptop").prop("checked", true);
      $("#given-laptop").prop("checked", false);
    }
    $("#editSubscriptionModal").modal("show");
  }

  function showDesc(e) {
    e.classList.toggle("single-line");
  }
  $(document).ready(() => {
    $("#student-filter-types").on("change", function(e) {
      const selected = $(e.target).find(":selected").val();
      const options = ["activatedStudent", "freezedStudent", "paymentTypeStudent", "restoredStudent", "defrostedStudent"];
      if (options.includes(selected)) {
        $("#student-filter-date-range").removeClass("d-none");
      } else {
        $("#student-filter-date-range").addClass("d-none");
      }
      if (selected === "paymentTypeStudent") {
        $("#student-filter-payment-types").removeClass("d-none");
      } else {
        $("#student-filter-payment-types").addClass("d-none");
      }
      if (selected === "extraFilter") {
        $(".extra-filter").removeClass("d-none");
      } else {
        $(".extra-filter").addClass("d-none");
      }
    });
    $("#student-filter-subscription-withdraw-select").on("change", (e) => {
      const selected = $(e.target).find(":selected").val();
      if (selected === "free") {
        $("#student-filter-price").addClass("d-none");
      } else {
        $("#student-filter-price").removeClass("d-none");
      }
    });

    $("#balance-withdraw-type").on("input", (e) => {
      const selectedValue = $(e.target).find(":selected").val()
      if (selectedValue == "refund") {
        $("#refund-payment-types").slideDown(200);
        $("#refund-payment-types select").attr("disabled", false);
      } else {
        $("#refund-payment-types").slideUp(200);
        $("#refund-payment-types select").attr("disabled", true);
      }
    });
    $('select#discount-reason').on('input', (e) => {
      const selectedOptionValue = $('select#discount-reason').val();
      if (selectedOptionValue == 'other') {
        $('#add-price-comment').attr('required', true);
      } else {
        $('#add-price-comment').attr('required', false);
      }
    });
    $('[name=discount_option]').on('input', (e) => {
      const discountOptions = {
        "package-price":"package-price-option",
        "discount-price":"discount-price-option",
        "another-price":"another-price-option"
      };
      for (const listOption in discountOptions) {
        if ($(e.target).attr('id') == listOption) {
          $(`#${discountOptions[listOption]}`).removeClass('d-none');
          $(`#${listOption}-field`).attr('required', true);
        } else {
          $(`#${discountOptions[listOption]}`).addClass('d-none');
          $(`#${listOption}-field`).attr('required', false);
        }
      }
    });
    /*const onGroupSelect = () => {
      const groupSelected = $("#student-filter-groups-select").select2("data");
      const teachers = $("#student-filter-mentor-select");
      const seletedGroups = groupSelected.map((option) => option.element.dataset.teacher);
      teachers.val(seletedGroups).trigger("change");
    };
    $("#student-filter-groups-select").on("change", onGroupSelect);

    $("#student-filter-mentor-select").on("change", (e) => {
      const groupSelected = $("#student-filter-groups-select").select2("data");
      const teacherSelected = $("#student-filter-mentor-select").select2("data");
      const selectedTeachers = teacherSelected.map((teacher) => teacher.element.value);
      const filterGroups = groupSelected.map((group) => {
        return selectedTeachers.includes(group.element.dataset.teacher) && group.element.attributes.value.value;
      });
      $("#student-filter-groups-select").off("change", null, onGroupSelect);
      $("#student-filter-groups-select").val(filterGroups).trigger("change");
      $("#student-filter-groups-select").on("change", onGroupSelect);
    }); */
  })
</script>