<?php
$db = mysqli_connect('localhost', 'root', '', 'registration');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test SELECT permission
$test_select = mysqli_query($db, "SELECT 1 FROM users LIMIT 1");
echo "SELECT permission: " . ($test_select ? "OK" : "Failed") . "\n";

// Create a test user first
$test_username = "test_user_" . time();
$test_email = "test" . time() . "@test.com";
$test_password = md5("testpassword");

$create_user = mysqli_query($db, "INSERT INTO users (username, email, password) 
                                 VALUES ('$test_username', '$test_email', '$test_password')");
if (!$create_user) {
    die("Failed to create test user: " . mysqli_error($db));
}

// Get the ID of the newly created user
$user_id = mysqli_insert_id($db);
echo "Created test user with ID: $user_id\n";

// Test INSERT permission with valid user_id
$test_insert = mysqli_query($db, "INSERT INTO user_activity_logs (user_id, activity_type) 
                                 VALUES ($user_id, 'login')");
echo "INSERT permission: " . ($test_insert ? "OK" : "Failed") . "\n";

// Test UPDATE permission
$test_update = mysqli_query($db, "UPDATE user_activity_logs 
                                 SET activity_type = 'logout' 
                                 WHERE user_id = $user_id LIMIT 1");
echo "UPDATE permission: " . ($test_update ? "OK" : "Failed") . "\n";

// Test DELETE permission - Delete in correct order
$test_delete_logs = mysqli_query($db, "DELETE FROM user_activity_logs 
                                      WHERE user_id = $user_id");
echo "DELETE from logs permission: " . ($test_delete_logs ? "OK" : "Failed") . "\n";

// Test EXECUTE permission with DeleteUser procedure
$test_execute = mysqli_query($db, "CALL DeleteUser($user_id)");
echo "EXECUTE permission: " . ($test_execute ? "OK" : "Failed") . "\n";

// Verify user has been moved to archived_users
$check_archived = mysqli_query($db, "SELECT * FROM archived_users WHERE id = $user_id");
$archived_user = mysqli_fetch_assoc($check_archived);

// Verify user no longer exists in users table
$check_deleted = mysqli_query($db, "SELECT * FROM users WHERE id = $user_id");
$original_user = mysqli_fetch_assoc($check_deleted);

echo "User archived: " . ($archived_user ? "YES" : "NO") . "\n";
echo "User removed from users table: " . (!$original_user ? "YES" : "NO") . "\n";

mysqli_close($db);
?>