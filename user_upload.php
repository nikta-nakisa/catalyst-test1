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

    $file_name = $user_options['file'];

    // Check if help option exists, echo directive and descriptions
    if (isset($user_options['help'])) {
        echo($help_directives);
        die();
    }
    // Create users table and exit
    if (isset($user_options['create_table'])) {
        $conn = database_manager();

        die();
    }

    if (!isset($user_options['file'])) {
        die('Please specify your csv file name');
    }
    $file_name = $user_options['file'];
    $lines = file_content_handler($file_name);

    foreach ($lines as $line) {
        $name = $surname = $email = '';
        $fields = explode(',', $line);

        $name = pares_data($fields[0], 'NAME');
        $surname = parse_data($fields[1], 'NAME');
        $email = parse_data($fields[2], 'EMAIL');

        if (!isset($user_options[''])) {
            // Insert new record into users tables
        }
    }



    // Open the file and parse it

    // Check if records should be added to database or not

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
        $name_pattern = '/[!@#$%^&*0-9]/';
        switch ($type) {
            case 'NAME':
                $parsed_data = ucfirst(strtolower($data));
                $parsed_data = preg_replace('/[!@#$%^&*0-9]/', '', $parsed_data, -1);
                break;
            case 'EMAIL':
                if (filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
                    die("Invalid email address : $data in CSV file");
                }
                $parsed_data = $data;
                break;
            default:
                break;
        }
        return $parsed_data;
    }

    function database_manager() {
        $user_name = $user_options['u'] ?? '';
        $password = $user_options['p']  ?? '';
        $host_name = $user_options['h'] ?? '';

        if (empty($user_name) || empty($password) || empty($host_name)) {
            die("MySql Connection details are not provided\n");
        }
        $conn = new mysqli($host_name, $user_name, $password);

    // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }