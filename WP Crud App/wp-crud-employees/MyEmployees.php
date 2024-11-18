<?php 

class MyEmployees{

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
    public function callPluginActivationFunctions(){

        $collate = $this->wpdb->get_charset_collate();

        $createCommand = "
            CREATE TABLE `".$this->table_name."` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `email` varchar(50) DEFAULT NULL,
            `designation` varchar(50) DEFAULT NULL,
            `profile_image` varchar(220) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ".$collate."
        ";

        require_once (ABSPATH. "/wp-admin/includes/upgrade.php");

        dbDelta($createCommand);

        //Creating WP Page
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

    public function dropEmployeesTable() { 
        $delete_command = "DROP TABLE IF EXISTS {$this->table_name}";
        $this->wpdb->query($delete_command);
    }

    // Render Employee Form Layout
    public function createEmployeeForm() {
        $message = "<h1>Employee Form</h1>";
        return $message;
    }
}?>