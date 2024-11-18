<form action="javascript:void(0)" id="frm_add_employee" enctype="multipart/form-data">
    <p>
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Employee name" id="name">
    </p>
    <p>
        <label for="">Email</label>
        <input type="email" name="email" placeholder="Employee Email" id="email">
    </p>
    <p>
        <label for="designation">Designation</label>
        <select name="designation" id="designation">
            <option value="">-- Choose Designation --</option>
            <option value="php">PHP Developer</option>
            <option value="java">Java Script Develeoper</option>
            <option value="wordpress">WordPress Develeoper</option>
            <option value="fullstack">Full Stack Develeoper</option>
        </select>
    </p>
    <p>
        <label for="file">Profile Image</label>
        <input type="file" name="file" id="file">
    </p>
</form>