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
print
$user_name = $user_options['u'] ?? '';
$password  = $user_options['p'] ?? '';
$host_name = $user_options['h'] ?? '';
if (empty($user_name) || empty($password) || empty($host_name)) {
    die("MySql Connection details are not provided\n");
}
$conn = new mysqli($host_name, $user_name, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if new database table should be created
//if (isset()) {

//}

// Open the file and parse it

// Check if records should be added to database or not

//