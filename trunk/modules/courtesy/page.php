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


include_once( 'kernel/common/template.php' );

$tpl = templateInit();

$Result = array();
$Result['content'] = $tpl->fetch("design:courtesy/page.tpl");

?>