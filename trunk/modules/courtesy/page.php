<?php

include_once( 'kernel/common/template.php' );

$tpl = templateInit();

$Result = array();
$Result['content'] = $tpl->fetch("design:courtesy/page.tpl");

?>