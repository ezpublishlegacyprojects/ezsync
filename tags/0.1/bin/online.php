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