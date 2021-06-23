<?php
# --- GAC Config file ---

$host = $_SERVER['SERVER_ADDR']=='::1' ?  '127.0.0.1' : $_SERVER['SERVER_ADDR'];
define('GAC_DATABASE_SERVER', $host);

// Change ME !
define('GAC_DATABASE_USER' , 'root');
define('GAC_DATABASE_PASSWORD', 'cedrix');

define('GAC_DATABASE_NAME', 'gac_tickets');
$bdObj = null;
$usedDb = 'mysql';



// Database hostname (usually "localhost")
define('M_DBHOST', GAC_DATABASE_SERVER);

// Database user
define('M_DBUSER', GAC_DATABASE_USER);

// Database password
define('M_DBPASSWORD', GAC_DATABASE_PASSWORD);

// Database name
define('M_DBNAME', GAC_DATABASE_NAME);



// DEBUG (log,debug or false) 
define('M_LOG','');

// Path for error log
define('M_TMP_DIR','/tmp/errors.log');

// FirePHP (false or true)
define('M_FIREPHP',false);

define('ABS_ROOT_PATH', realpath(dirname(__FILE__)));
define('ABS_CLASSES_PATH', ABS_ROOT_PATH . '/classes/');
define('ABS_CONTROLLERS_PATH', ABS_ROOT_PATH . '/controllers/');
define('ABS_SCRIPTS_PATH', ABS_ROOT_PATH . '/js/');
define('ABS_GENERAL_PATH', ABS_ROOT_PATH . '/general/');
define('ABS_DATA_PATH', ABS_ROOT_PATH . '/sql/');
define('ABS_DUMP_PATH', ABS_ROOT_PATH . '/sql/dump/');
define('ABS_STYLES_PATH', ABS_ROOT_PATH . '/styles/');
define('ABS_IMAGES_PATH', ABS_ROOT_PATH . '/styles/img/');

define('APPLI_PATH', '/planning/');
define('ROOT_PATH', $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']. APPLI_PATH);
define('CLASSES_PATH',  'classes/');
define('CONTROLLERS_PATH',  'controllers/');
define('SCRIPTS_PATH',  'js/');
define('GENERAL_PATH',  'general/');
define('DATA_PATH',  'sql/');
define('STYLES_PATH',  'styles/');
define('IMAGES_PATH',  'styles/img/');
define('MAIN_IMAGES_PATH',  '../../styles/img/');

// Database preset
$dbObj = null;
switch($usedDb){
    case 'mysql':
        define('GAC_DATABASE_DRIVER', 'mysql');
        define('GAC_DATABASE_PORT', 3306);
        $dbFile = 'DbPdo.php';
        include_once ABS_CLASSES_PATH.$dbFile;
        $dbObj = new DbPdo();
     break;
 
    default:
        // mySqli
        define('GAC_DATABASE_DRIVER', 'mysqli');
        define('GAC_DATABASE_PORT', 5432);
        $dbFile = 'DbMysqli.php';
        include_once ABS_CLASSES_PATH.$dbFile;
        $dbObj = new DbMySqli();
    break;
}
// Database driver (mysql, pgsql)
define('M_DBDRIVER', GAC_DATABASE_DRIVER);
define('M_DBPORT', GAC_DATABASE_PORT);


// Dates
define('DATE_FORMAT', 'd/m/Y'); // voir aussi la fonction getDate() de /public/js/main.js pour le côté client
define('DB_DATE_FORMAT', 'Y-m-d'); // MySQL
define('DB_DATETIME_FORMAT', 'Y-m-d H:i:s'); // MySQL
// Erreurs
define('DB_NOPRESENT_ERROR', 'La base de données GAC_planning\'existe pas ou n\'est pas visible par le serveur Web');
define('TABLE_NOPRESENT_ERROR', 'La base de données GAC_planning n\'existe pas ou n\'est pas visible par serveur Web');
