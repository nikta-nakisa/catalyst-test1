    <?php
    $help_directives = "\n
    --file [csv file name] – this is the name of the CSV to be parsed\n
    --create_table – this will cause the MySQL users table to be built (and no further action will be taken)\n
    --dry_run – this will be used with the --file directive in case we want to run the
    script but not insert into the DB. All other functions will be executed, but the
    database won't be altered\n
    -u – MySQL username\n
    -p – MySQL password\n
    -h – MySQL host\n
    --help \n ";

    $options = '';
    $options .= 'u:';
    $options .= 'p:';
    $options .= 'h:';
    $options .= 'abc';

    $long_options = array(
        'file:',
        'optional::',
        'create_table',
        'help',
        'dry_run',
    );
    $user_options = getopt($options, $long_options);

    // Check if help option exists, echo directive and descriptions
    if (isset($user_options['help'])) {
        echo($help_directives);
        die();
    }
    // Create users table and exit
    if (isset($user_options['create_table'])) {
        die(create_database_table());
    }

    if (!isset($user_options['file'])) {
        die("Please specify your csv file name.\n");
    }
    $file_name = $user_options['file'];
    $lines = file_content_handler($file_name);
    //removing headers from CSV file
    array_shift($lines);
    $insert_record_flag = isset($user_options['dry_run']) ? false : true;

    // Make sure users table exist
    if ($insert_record_flag) {
        $conn = database_connector();
        $conn->begin_transaction();
        create_database_table($conn);
    }

    foreach ($lines as $line) {
        $name = $surname = $email = '';
        $fields = explode(',', $line);

        $name    = parse_data($fields[0], 'NAME');
        $surname = parse_data($fields[1], 'NAME');
        if (!($email = parse_data($fields[2], 'EMAIL'))) {
            if ($insert_record_flag) {
                $conn->rollback();
            }
            die("Invalid email $fields[2] in CSV file.\n");
        }

        if ($insert_record_flag) {
            // Insert new record into users table
            insert_new_record($name, $surname, $email, $conn);
        }
    }
    if ($insert_record_flag) {
        $conn->commit();
        $conn->close();
    }


    // Open the file and parse it
    function file_content_handler($file_name) {
        $lines = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            die('An error happened or your file is empty.');
        }
        return $lines;
    }

    function parse_data($data, $type) {
        $parsed_data = '';
        $data = trim($data);
        switch ($type) {
            case 'NAME':
                $parsed_data = ucfirst(strtolower($data));
                $parsed_data = preg_replace('/[!@#$%^&*0-9]/', '', $parsed_data, -1);
                break;
            case 'EMAIL':
                if (filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
                    return false;
                }
                $parsed_data = $data;
                break;
            default:
                break;
        }
        return $parsed_data;
    }

    function database_connector() {
        global $user_options;
        $user_name = $user_options['u'] ?? '';
        $password  = $user_options['p'] ?? '';
        $host_name = $user_options['h'] ?? '';

        if (empty($user_name) || empty($password) || empty($host_name)) {
            die("MySql Connection details are not provided\n");
        }
        $conn = new mysqli($host_name, $user_name, $password, 'homestead', '3306');
        // Check database connection
        if (!$conn) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    function create_database_table($conn = null) {
        $close_connection_flag = false;

        if (is_null($conn)) {
            $conn = database_connector();
            $close_connection_flag = true;
        }
        $result = $conn->query('CREATE TABLE IF NOT EXISTS c_users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            surname VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');
        if ($close_connection_flag) {
            $conn->close();
        }
        if ($result) {
            return("users table is created \n");
        } else {
            return($conn->error . "\n");
        }
    }

    function Insert_new_record($name, $surname, $email, $conn = null) {
        $close_connection_flag = false;

        if (is_null($conn)) {
            $conn = database_connector();
            $close_connection_flag = true;
        }
        $query = 'INSERT INTO c_users(name,surname,email) VALUES("' . $name . '","' . $surname . '","' . $email . '")';
        $conn->query($query);

        if ($close_connection_flag) {
            $conn->close();
        }

    }