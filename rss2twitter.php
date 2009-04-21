<?php

define("R2T_PROJECT_DIR",dirname(__FILE__));
ini_set("include_path",R2T_PROJECT_DIR."/inc/:".ini_get("include_path"));
include("r2t.php");

$r2t = new r2t();
$r2t->process();


