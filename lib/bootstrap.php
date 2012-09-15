<?
require_once('Configure.php');
require_once('constants.php');

// set internal encoding with utf-8.
mb_internal_encoding('UTF-8');

// set timezone
date_default_timezone_set('Asia/Tokyo');

// set include path for batch processing.
// it is better not to set using htaccess.
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../lib'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../lib/core'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../lib/core/db'));
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../lib/contrib'));
