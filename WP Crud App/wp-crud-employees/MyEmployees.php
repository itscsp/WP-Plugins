<?php

class MyEmployees
{

    private $wpdb;
    private $table_name;
    private $table_prefix;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $this->wpdb->prefix; // wp_
        $this->table_name = $this->table_prefix . "employees_table"; // wp_employees_table
    }

    // Create DB Table + WordPress Page
    public function callPluginActivationFunctions()
    {

        $collate = $this->wpdb->get_charset_collate();

        $createCommand = "
            CREATE TABLE `" . $this->table_name . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `email` varchar(50) DEFAULT NULL,
            `designation` varchar(50) DEFAULT NULL,
            `profile_image` varchar(220) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) " . $collate . "
        ";

        require_once(ABSPATH . "/wp-admin/includes/upgrade.php");

        dbDelta($createCommand);

        //Creating WP Page
        $page_title = "Employee CRUD System";
        $page_content = "[wp-employee-form]";

        if (!get_page_by_title($page_title)) {
            wp_insert_post(array(
                "post_title" => $page_title,
                "post_content" => $page_content,
                "post_type" => "page",
                "post_status" => "publish"
            ));
        }
    }

    public function dropEmployeesTable()
    {
        $delete_command = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($delete_command);
    }

    // Render Employee Form Layout
    public function createEmployeeForm()
    {
        ob_start();

        include_once WCE_DIR_PATH . "template/employee_form.php";

        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }


    //Add script and style to link to app
    public function addAssetsToPlugin()
    {
        // Styles
        wp_enqueue_style("employee-crud-css", WCE_DIR_URL . "assets/styles.css");

        //Validation JS
        wp_enqueue_script("validation-form", WCE_DIR_URL . "assets/jquery.validate.min.js", array('jquery'));

        //Script
        wp_enqueue_script("employee-crud-script", WCE_DIR_URL . "assets/script.js", array("jquery"), 1);

        //Localize Ajax admin url
        wp_localize_script("employee-crud-script", "wce_object", array(
            "ajax_url" => admin_url("admin-ajax.php"),
        ));
    }

    //Process Ajax request: Add Employee Form
    public function handleAddEmployeeFormData()
    {

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_text_field($_POST['email']);
        $designation = sanitize_text_field($_POST['designation']);
        $profile_image =  "";

        //Handle files
        if (isset($_FILES['profile_image']['name'])) {
            $UploadFile = $_FILES['profile_image'];

            // $UploadFile['name'] - employee-1.webp

            // Original File Name
            $originalFileName = pathinfo($UploadFile['name'], PATHINFO_FILENAME); // employee-1

            // File Extension
            $file_extension = pathinfo($UploadFile['name'], PATHINFO_EXTENSION); // webp

            // New Image Name
            $newImageName = $originalFileName . "_" . time() . "." . $file_extension; // employee-1_89465133.webp

            $_FILES['profile_image']['name'] = $newImageName;

            $fileUploaded = wp_handle_upload($_FILES['profile_image'], array('test_form' => false));

            $profile_image = $fileUploaded['url'];
        }

        /**
         * array('test_form' => false) : wp_handle_upload is not going to check any file attributes or even file submission
         * 
         * array('test_form' => true) : wp_handle_upload will validate from request, nonce value and other form paramters 
         */

        //Insert data into table

        $this->wpdb->insert($this->table_name, [
            "name" => $name,
            "email" => $email,
            "designation" => $designation,
            "profile_image" => $profile_image,
        ]);

        $employee_id = $this->wpdb->insert_id;

        if ($employee_id > 0) {

            echo json_encode([
                'status' => 1,
                "message" => "Employee created and insert into database.",
                "data" => $employee_id
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                "message" => "Something went wrong, Try again.",
            ]);
        }


        die;
    }

    //Load employee data into frontend
    public function handleLoadEmployeeData()
    {
        $employees = $this->wpdb->get_results(
            "SELECT * FROM {$this->table_name}",
            ARRAY_A
        );

        return wp_send_json([
            "status" => true,
            "messsage" => "Employee Data",
            "employees" => $employees
        ]);

        exit;
    }

    // Delete employee data 
    public function handleDeleteEmployeeData()
    {
        $employee_id = $_GET['empId'];

        $this->wpdb->delete($this->table_name, [
            'id' => $employee_id
        ]);

        return wp_send_json([
            "status" => true,
            "message" => "Employee Deleted Successfully"
        ]);
        exit;
    }

    // Get employee data by id
    public function getEmployeeDataById()
    {

        $employee_id = $_GET['empId'];

        if ($employee_id > 0) {
            $employeeData = $this->wpdb->get_row(
                "SELECT * FROM {$this->table_name} WHERE id = {$employee_id}",
                ARRAY_A
            );

            return wp_send_json([
                'status' => 1,
                "message" => "Emploayee data for ID: ${$employee_id}",
                'data' => $employeeData
            ]);
        } else {

            return wp_send_json([
                'status' => 0,
                "message" => "Something went wrong, Try again.",
            ]);
        }

        exit;
    }

    //Update user data
    public function handleUpdateEmployeeData()
    {
        $id = sanitize_text_field($_POST["employee_id"]);
        $name = sanitize_text_field($_POST["employee_name"]);
        $email = sanitize_text_field($_POST["employee_email"]);
        $designation = sanitize_text_field($_POST["employee_designation"]);

        $employeeData = $this->getEmployeeData($id);

        $profile_image_url = "";


        if (!empty($employeeData)) {

            $profile_image_url = $employeeData['profile_image'];

            $profile_image_image = isset($_FILES['employee_profile_image']['name']) ? isset($_FILES['employee_profile_image']['name']) : "";

            if (!empty($profile_image_image)) {
                //Profile Image
                //Handle files

                if (!empty($profile_image_url)) {
                    // http://localhost/wp/wp_plugin_course/wp-content/uploads/2024/08/employee-1.webp
                    $wp_site_url = get_site_url(); //// http://localhost/wp/wp_plugin_course/
                    $file_path = str_replace($wp_site_url . '/', "", $profile_image_url); //wp-content/uploads/2024/08/employee-1.webp

                    if (file_exists(ABSPATH . $file_path)) {
                        //Remove the file from uploads folder
                        unlink(ABSPATH . $file_path);
                    }
                    $UploadFile = $_FILES['profile_image_image'];
    
                    // $UploadFile['name'] - employee-1.webp
    
                    // Original File Name
                    $originalFileName = pathinfo($UploadFile['name'], PATHINFO_FILENAME); // employee-1
    
                    // File Extension
                    $file_extension = pathinfo($UploadFile['name'], PATHINFO_EXTENSION); // webp
    
                    // New Image Name
                    $newImageName = $originalFileName . "_" . time() . "." . $file_extension; // employee-1_89465133.webp
    
                    $_FILES['profile_image']['name'] = $newImageName;
                }


                // //$UploadFile['name'] - image-name.webp

                //Upload New Image
                $fileUploaded = wp_handle_upload($_FILES['employee_profile_image'], array('test_form' => false));
                $profile_image_url = $fileUploaded['url'];
            }

            $this->wpdb->update($this->table_name, [
                "name" => $name,
                "email" => $email,
                "designation" => $designation,
                "profile_image" => $profile_image_url,
            ], [
                "id" => $id
            ]);

            return wp_send_json([
                "status" => true,
                "message" => "Employee updated successfully"
            ]);
        } else {
            return wp_send_json([
                'status' => false,
                'message' => "No Employee found wih this ID"
            ]);
        }
    }

    //Get empployee Data
    private function getEmployeeData($employee_id)
    {
        $employeeData = $this->wpdb->get_row(
            "SELECT * FROM ($this->table_name) WHERE id = ($employee_id)",
            ARRAY_A
        );

        return $employeeData;
    }
}
