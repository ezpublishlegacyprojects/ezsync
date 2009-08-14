<?php
// SOFTWARE NAME: eZ Deploy
// SOFTWARE RELEASE: 0.1
// COPYRIGHT NOTICE: Copyright (C) 2008 idaeto srl
// SOFTWARE LICENSE: GNU General Public License v3.0
// AUTHOR: Francesco (cphp) Trucchia - ft@ideato.it
// NOTICE: >
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//


require_once('autoload.php');

function run_sync($dry_run = true, $host, $dir, $user, $port, $parameters, $cli, $script, $file_rsync_exclude)
{

  if (!file_exists($file_rsync_exclude)){
    $script->shutdown(1, 'You must create a rsync_exclude file for your extension.');
  }

  if (substr($dir, -1) != '/')
  {
    $dir .= '/';
  }

  if($user != ''){
    $user .= '@';
  }

  $ssh = 'ssh';

  if($port != ''){
    $ssh .= ' -p '.$port.' ';
  }


  if($parameters == '')
  {
    $parameters = '-azC --force --delete';
    if (file_exists($file_rsync_exclude))
    {
      $parameters .= ' --exclude-from='.$file_rsync_exclude;
    }

    /*if (file_exists('config/rsync_include.txt'))
    {
      $parameters .= ' --include-from=config/rsync_include.txt';
    }

    if (file_exists('config/rsync.txt'))
    {
      $parameters .= ' --files-from=config/rsync.txt';
    }*/
  }


  $dry_run = (($dry_run === true) ? '--dry-run' : '');

  $cmd = "rsync --progress $dry_run $parameters -e '$ssh' ./ $user$host:$dir";

  $cli->output(execute($cmd, $script));
  
}

function execute($cmd, $script){
  ob_start();
  passthru($cmd.' 2>&1', $return);
  $content = ob_get_contents();
  ob_end_clean();

  if ($return > 0)
  {
    $script->shutdown(1, sprintf('Problem executing command %s', "\n".$content));
  }

  return $content;
}


$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( "Sync script to deploy eZ Publish Project."),
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true) );

$script->startup();

$options = $script->getOptions( "[go][env;]",
                                "",
                                array(	'go'	=> 'Really sync, without simulating',
                                		'env'	=> 'Sync specified enviroment'));


                                
$script->initialize();

$ini = eZINI::instance('sync.ini');

$defaultEnvSection = 'DefaultSyncSettings';
$envSection = '';

if($options['env'] && $ini->hasSection(ucfirst($options['env']).'SyncSettings')){
	$envSection = ucfirst($options['env']).'SyncSettings';
}
else{
	$envSection = $defaultEnvSection;
}


$host = $ini->hasvariable($envSection, 'Host') ? $ini->variable($envSection, 'Host') : $ini->variable($defaultEnvSection, 'Host');
$dir = $ini->hasvariable($envSection, 'Dir') && $ini->variable($envSection, 'Dir') != '' ? $ini->variable($envSection, 'Dir') : new Exception('You need to select your server dir');
$user = $ini->hasvariable($envSection, 'User') ? $ini->variable($envSection, 'User') : $ini->variable($defaultEnvSection, 'User'); 
$port = $ini->hasvariable($envSection, 'Port') ? $ini->variable($envSection, 'Port') : $ini->variable($defaultEnvSection, 'Port'); 
$parameters = $ini->hasvariable($envSection, 'Parameters') ? $ini->variable($envSection, 'Parameters') : $ini->variable($defaultEnvSection, 'Parameters'); 
$file_rsync_exclude = $ini->hasvariable($envSection, 'FileRsyncExclude') ? $ini->variable($envSection, 'FileRsyncExclude') : $ini->variable($defaultEnvSection, 'FileRsyncExclude');

echo "Connecting to ",$user,"@",$host,":",$port."\n";
echo "Sync dir ",$dir,"\n";

$dry_run = true;

if($options['go']){
  $dry_run = false;
}

run_sync($dry_run, $host, $dir, $user, $port, $parameters, $cli, $script, $file_rsync_exclude);

$cli->output( "Sync completed" );
return $script->shutdown();


?>
