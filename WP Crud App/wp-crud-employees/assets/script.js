console.log("I am loading");

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
          jQuery("#frm_add_employee")[0].reset();
          loadEmployee()
        }
      },
    });
  });

  loadEmployee();
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

      jQuery('#employee_data_tbody').html(employeesDataHTML);
    },
  });
}
