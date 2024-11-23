jQuery(document).ready(function ($) {
  //Add form validation
  $("#frm_add_employee").validate("");

  // Form Submit Handler
  jQuery("#frm_add_employee").on("submit", function (event) {
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

  //Open Add Empployee Form
  jQuery(document).on("click", "#btn_open_add_employee_form", function () {
    jQuery("#addEmployeLayout").toggleClass("hide_element");
    jQuery(this).addClass("hide_element");


  });

  jQuery(document).on("click", "#btn-close_add_employee_form", () => {
    jQuery("#addEmployeLayout").toggleClass("hide_element");
    jQuery("#btn_open_add_employee_form").removeClass("hide_element");
  });

  jQuery(document).on("click", ".btn_edit_employee", () => {
    jQuery("#editEmployeLayout").toggleClass("hide_element");
    jQuery("#close_add_employee_form").removeClass("hide_element");
  });

  
});

let deleteLoadedDataLoaded = false;

function deleteData() {
  jQuery(".btn_delete_employee").on("click", function () {
    console.log("I am here... ");
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
}

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

      /*

            */

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
      if (!deleteLoaded) {
        deleteData();
        deleteLoaded = true;
      }
    },
  });
}
