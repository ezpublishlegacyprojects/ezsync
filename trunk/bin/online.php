<?php

require_once('autoload.php');

$original_htaccss = '.htaccess';
$backup_htaccess  = '.htaccess.backup';
$offline_htaccess = 'extension/ezdeploy/settings/htaccess';

$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( "Make site offline to maintening operations."),
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true) );

$script->startup();

$options = $script->getOptions( "", "", array());
                                       
$script->initialize();

if (!copy($backup_htaccess, $original_htaccss)){
  $cli->notice('System is already online');
  $script->shutdown( 1 );
}

unlink($backup_htaccess);

$cli->output( "Site online" );

return $script->shutdown();

?>