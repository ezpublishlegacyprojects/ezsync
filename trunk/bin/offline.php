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

$ini = eZINI::instance('offline.ini');

if (!copy($original_htaccss, $backup_htaccess)){
  $cli->error( "Impossible to copy $original_htaccss to $backup_htaccess" );
  $script->shutdown( 1 );
}

if(!copy($offline_htaccess, $original_htaccss)){
  $cli->error( "Impossible to copy $offline_htaccess to $original_htaccss" );
  $script->shutdown( 1 );
}

$cli->output( "Site offline" );

return $script->shutdown();

?>