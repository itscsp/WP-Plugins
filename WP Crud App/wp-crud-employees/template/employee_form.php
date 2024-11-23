<div id="wp_employee_crud_plugin">
    <h3>Add Empployees</h3>

    <form action="javascript:void(0)" id="frm_add_employee" enctype="multipart/form-data">
        <input type="hidden" name="action" value="wce_add_employee">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" placeholder="Employee name" id="name" required>
        </div>
        <div class="form-group">
            <label for="">Email</label>
            <input type="email" name="email" placeholder="Employee Email" id="email" required>
        </div>
        <div class="form-group">
            <label for="designation">Designation</label>
            <select name="designation" id="designation" required>
                <option value="">-- Choose Designation --</option>
                <option value="php">PHP Developer</option>
                <option value="java">Java Script Develeoper</option>
                <option value="wordpress">WordPress Develeoper</option>
                <option value="fullstack">Full Stack Develeoper</option>
            </select>
        </div>
        <div class="form-group">

            <label for="file">Profile Image</label>
            <input type="file" name="profile_image" id="file">
        </div>
        <div class="form-group">

            <button id="btn_save_data" type="submit">Save Data</button>
        </div>
    </form>

    <!-- List Emplayee Layout -->
    <h3>List Empployees</h3>
    <table>
        <thead>
            <th>#ID</th>
            <th>#Name</th>
            <th>#Email</th>
            <th>#Designation</th>
            <th>#Profile Image</th>
            <th>#Action</th>
        </thead>
        <tbody id="employee_data_tbody">
           
        </tbody>
    </table>

    <!-- List Emplayee Layout End -->

</div>
