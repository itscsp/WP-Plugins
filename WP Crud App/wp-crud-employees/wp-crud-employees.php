<?php
/**
 * Plugin Name: WP Employees CRUD
 * Description: This plugin performs CRUD Operations with Employees Table. Also on Activation it will create a dynamic WordPress page and it will have a shortcode. 
 * Version: 1.0
 * Author: Chethan S Poojary
 */

// Prevent direct access
if (!defined("ABSPATH")) {
   exit;
}

// Define plugin paths and URLs
define("WCE_DIR_PATH", plugin_dir_path(__FILE__));
define("WCE_DIR_URL", plugin_dir_url(__FILE__));

// Include the main plugin class
include_once WCE_DIR_PATH . "MyEmployees.php";

// Create Class Object
$employeeObject = new MyEmployees;

// Register activation hook to create table and page
register_activation_hook(__FILE__, [$employeeObject, "callPluginActivationFunctions"]);

//Drop DB Table
register_deactivation_hook(__FILE__, [$employeeObject, 'dropEmployeesTable']);

// Register Shortcode
add_shortcode("wp-employee-form", [$employeeObject, "createEmployeeForm"])
?>