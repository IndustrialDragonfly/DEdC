<?php

/**
 * @author eugene
 * In order to support the use of our libraries, must set the include path to
 * the root of the project (i.e. the controller folder) at the start of the
 * test
 */
set_include_path(implode(PATH_SEPARATOR, array(realpath('../../..'), get_include_path())));

$GLOBALS['path'] = realpath('../../..');

// Classloaders of the various components
require_once 'ClassLoader.php';
require_once 'Models/ClassLoader.php';
require_once 'Models/DataModel/ClassLoader.php';
require_once 'Models/UserModel/ClassLoader.php';
require_once 'AuthenticationInformation/ClassLoader.php';
require_once 'Storage/ClassLoader.php';
require_once 'Interface/ClassLoader.php';

spl_autoload_register("DataModelClassLoader");
spl_autoload_register("ModelsClassLoader");
spl_autoload_register("InterfaceClassLoader");
spl_autoload_register("StorageClassLoader");
spl_autoload_register("UserModelClassLoader");
spl_autoload_register("AuthenicationInformationClassLoader");
// Must always be last in the stack so that it can handle failure to load
spl_autoload_register("GeneralLoader");
?>
