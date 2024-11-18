<?php

class MyEmployees
{

    private $wpdb;
    private $table_name;
    private $table_prefix;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $this->wpdb->prefix;
        $this->table_name = $this->table_prefix . "employees_table";
    }

    //Create DB Table
    public function createEmployeesTable()
    {

        $collate = $this->wpdb->get_charset_collate();

        $createCommand = "
            CREATE TABLE '".$this->table_name."' (
                'id' int(11) NOT NULL AUTO_INCREMENT,
                'name' varchar(50) NOT NULL,
                'email' varchar(50) DEFAULT NULL,
                'designation' varchar(50) DEFAULT NULL,
                PRIMARY KEY ('id')
            ) ".$collate."
        ";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

        dbDelta($createCommand);

        // Wp Page
        $page_title = "Employee CRUD System";
        $page_content = "[wp-employee-form]";

        if(!get_page_by_title($page_title)){
            wp_insert_post(array(
                "post_title" => $page_title,
                "post_content" => $page_content,
                "post_type" => "page",
                "post_status" => "publish"
            ));
        }
    }

//     //Drop table
//     public function dropEmployeesFrom() {
//         $delete_command = "DROP TABLE IF EXISTS ${$this->table_name}";
//         $this->wpdb->query($delete_command);
//     }

//     //Reder Employee From Layout
//     public function createEmployeesForm() {
//         ob_start();
//         include_once WCE_DIR_PATH . "template/employee_form.php";
//         $template = ob_get_contents();
//         ob_end_clean();
//         return $template;
//     }

//     //Add CSS / JS
//     public function addAssetsToPlugin() {
        
//         //Styles
//         wp_enqueue_styles("employee-crud-css", WCE_DIR_URL . "assets/styles.css");

//         //Validation
//         wp_enqueue_script("wce-validation", WCE_DIR_URL . 'assets/jquery.validate.min.js', array("jquery"));

//         // JS
//         wp_enqueue_script("employee-crud-js", WCE_DIR_URL . "assets/script.js", array("jquery"), "3.0");

//         wp_localize_script("employee-crud-js", "wce_object", array(
//             'ajax_url' => admin_url('admin-ajax.php')
//         ));
//     }

//     // Process Ajax Request: Add Employee From
//     public function handleAddEmployeeFormData() {

//         $name = sanitize_text_field($_POST['name']);
//         $email = sanitize_text_field($_POST['email']);
//         $designation = sanitize_text_field($_POST['designation']);

//         $profile_url = "";

//         /**
//          * 
//          * array("test_form" => false) -> wp_handle_upload is not going to check any file attributes or even file submission
//          * 
//          * array("test_form" => true) -> wp_handle_upload will validate form request from request, nonce value and other form parameters
//          * 
//          */

//          // Check for File
//          if(isset($_FILES['profile_image']['name'])) {

//         $UploadFile = $_FILES['profile_image'];
//         // $UploadFile = employee-1.webp
        
//         // Original File Name
//         $originalFileName = path


//     }
}
