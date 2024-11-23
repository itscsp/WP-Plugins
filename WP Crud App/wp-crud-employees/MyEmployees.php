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
}
