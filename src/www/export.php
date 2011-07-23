<?php
/*
 * export.php -
 * Copyright (c) 2003  OGP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** Set flag that this is a parent file */
define("_VALID_UA", 1); 

require_once("config.inc.php");

require_once("include/page.inc.php");
require_once("include/db.functions.php");
require_once("include/export.inc.php");
require_once("include/game_rules.php");
require_once("include/time.inc.php");
require_once("include/basic.lib.php");
require_once("include/movement.lib.php");
require_once("include/artefact.inc.php");


page_start();
init_buildings();
init_defenseSystems();
init_resources();
init_units();
init_sciences();

// open the template
$template = tmpl_open($_SESSION['player']->getTemplatePath() . 'export.ihtml');

$content = export_switch();
tmpl_set($template, 'content', $content );
echo tmpl_parse($template);


page_end();

?>