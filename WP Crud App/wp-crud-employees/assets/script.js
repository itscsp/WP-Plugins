jQuery(document).ready(function ($) {
  //Add form validation
  $("#frm_add_employee").validate("");

  // Form Submit Handler
  $("#frm_add_employee").on("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    jQuery.ajax({
      url: wce_object.ajax_url,
      data: formData,
      method: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.status) {
          alert(response.message);
          setTimeout(() => {
            jQuery("#frm_add_employee")[0].reset();
          }, 1500);
          loadEmployee();
        }
      },
    });
  });

  //Call load employee
  loadEmployee();

  //Delete records from database
  $(document).on("click", ".btn_delete_employee", function () {
    // Use a regular function
    let empId = jQuery(this).data("id"); // This will correctly refer to the clicked button

    if (confirm("Are you sure you want to delete?")) {
      // If true
      jQuery.ajax({
        url: wce_object.ajax_url,
        data: {
          action: "wce_delete_employee",
          empId: empId,
        },
        method: "GET",
        dataType: "json",
        success: (response) => {
          console.log(response);
          if (response) {
            alert(response.message);
            setTimeout(() => {
              loadEmployee();
            }, 1000);
          }
        },
      });
    }
  });

  //Open Add Empployee Form
  /**
   * When user click on add Employee
   * - Hide Edit form if open
   * - Add close form button active
   *
   * When user click on Edit employee
   * - Close Add form
   * - Hide Add Employee button
   *
   */

  // To show employee form and remove employee btn
  $("#btn_open_add_employee_form").on("click", () => {
    $("#addEmployeLayout").removeClass("hide_element");
    $("#btn_open_add_employee_form").addClass("hide_element");
  });

  //Hide employee form and show add employee btn
  $("#btn-close_add_employee_form").on("click", () => {
    console.log("Hello");
    $("#addEmployeLayout").addClass("hide_element");
    $("#btn_open_add_employee_form").removeClass("hide_element");
  });

  // To show Edit Employee form and add employee btn
  $(document).on("click", ".btn_edit_employee", function () {
    $("#editEmployeLayout").removeClass("hide_element");
    $("#btn_open_add_employee_form").addClass("hide_element");

    // Get existing data by employee id and insert into form
    let employee_id = $(this).data("id");

    jQuery.ajax({
      url: wce_object.ajax_url,
      data: {
        action: "wce_get_employee_by_id",
        empId: employee_id,
      },
      method: "",
      dataType: "",
      success: function (response) {
        let employee_data = response.data;
        jQuery("#employee_name").val(employee_data.name);
        jQuery("#employee_email").val(employee_data.email);
        jQuery("#employee_designation").val(employee_data.designation);
        jQuery("#employee_profile_icon").attr("src", employee_data.profile_image);
        jQuery("#employee_id").val(employee_data.id);
      },
    });
  });

  // To hide edit employee form and show add employee form
  $("#btn-close_edit_employee_form").on("click", () => {
    $("#editEmployeLayout").addClass("hide_element");
    $("#btn_open_add_employee_form").removeClass("hide_element");
  });

  $("#frm_edit_employee").on("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    jQuery.ajax({
      url: wce_object.ajax_url,
      data: formData,
      method: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.status) {
          alert(response.message);
          setTimeout(() => {
            loadEmployee();
            $("#editEmployeLayout").addClass("hide_element");
            $("#btn_open_add_employee_form").removeClass("hide_element");
          }, 1000);
        }
      },
    });
  });
});

//Load all employee data from table
function loadEmployee() {
  jQuery.ajax({
    url: wce_object.ajax_url,
    data: {
      action: "wce_load_employees_data",
    },
    method: "GET",
    dataType: "json",
    success: (response) => {
      console.log(response);

      var employeesDataHTML = "";

      jQuery.each(response.employees, (index, employee) => {
        console.log(employee.name);
        employeesDataHTML += `
            <tr>
                <td>${employee.id}</td>
                <td>${employee.name}</td>
                <td>${employee.email}</td>\
                <td>${employee.designation}</td>
                <td><img width="100px" src="${employee.profile_image}" /></td>
                <td>
                    <button data-id="${employee.id}" class="btn_edit_employee">Edit</button>
                    <button data-id="${employee.id}" class="btn_delete_employee">Delete</button>
                </td>
            </tr>
        `;
      });

      jQuery("#employee_data_tbody").html(employeesDataHTML);
    },
  });
}
