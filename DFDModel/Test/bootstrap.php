<?php

/**
 * @author eugene
 * In order to support the use of our libraries, must set the include path to
 * the root of the project (i.e. the controller folder) at the start of the
 * test
 */
set_include_path(implode(PATH_SEPARATOR, array(realpath('../..'), get_include_path())));
?>
