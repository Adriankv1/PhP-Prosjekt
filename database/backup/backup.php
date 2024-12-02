<?php
class DatabaseBackup {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'registration';
    private $conn;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        // Check for connection errors
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to create a backup of the database
    public function createBackup() {
        try {
            $backup = '';
            
            // Get all tables in the database
            $result = $this->conn->query("SHOW TABLES");
            while ($row = $result->fetch_array()) {
                $table = $row[0];
                
                // Get the create table statement for the current table
                $show_create = $this->conn->query("SHOW CREATE TABLE $table");
                $create_table = $show_create->fetch_array();
                $backup .= "\n\n" . $create_table[1] . ";\n\n";
                
                // Get all data from the current table
                $result_data = $this->conn->query("SELECT * FROM $table");
                
                // Loop through each row of data
                while ($row_data = $result_data->fetch_assoc()) {
                    $columns = array();
                    $values = array();
                    
                    // Loop through each column value
                    foreach ($row_data as $value) {
                        if ($value === null) {
                            $values[] = "NULL";
                        } else {
                            $values[] = "'" . $this->conn->real_escape_string($value) . "'";
                        }
                    }
                    
                    // Create the insert statement for the current row
                    $backup .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
                }
                
                $backup .= "\n";
            }
            
            // Store the backup in the db_backup table
            $stmt = $this->conn->prepare("INSERT INTO db_backup (backup_data) VALUES (?)");
            $stmt->bind_param("s", $backup);
            
            // Execute the statement and check for errors
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'backup_id' => $this->conn->insert_id
                ];
            } else {
                throw new Exception("Failed to store backup");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage() . ": " . $this->conn->error
            ];
        }
    }

    // Method to get the latest backup from the db_backup table
    public function getLatestBackup() {
        $result = $this->conn->query("SELECT * FROM db_backup ORDER BY created_at DESC LIMIT 1");
        return $result->fetch_assoc();
    }

    // Destructor to close the database connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Test the backup
try {
    $backup = new DatabaseBackup();
    $result = $backup->createBackup();
    
    if ($result['success']) {
        echo "Backup created successfully with ID: " . $result['backup_id'];
        
        // Check the latest backup
        $latest = $backup->getLatestBackup();
        if ($latest) {
            echo "\nLatest backup was created at: " . $latest['created_at'];
        }
    } else {
        echo "Backup failed: " . $result['message'];
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>