<?php
/*
 * This file is part of Contao Open ER2 Client API.
 *
 * Contao Open ER2 Client API is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * Contao Open ER2 Client API is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Contao Open ER2 Client API.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Example file how to use the OpenER2 client inside of contao.
 */

$client = new \OpenER2\Client();

// set wsdl url
$client->setWsdl($GLOBALS['TL_CONFIG']['repository_wsdl']);

// set database settings
$client->setDatabaseDsn(strtolower($GLOBALS['TL_CONFIG']['dbDriver'])
	. ':dbname=' . $GLOBALS['TL_CONFIG']['dbDatabase']
	. ';host=' . $GLOBALS['TL_CONFIG']['dbHost']
	. ';port=' . $GLOBALS['TL_CONFIG']['dbPort']);
$client->setDatabaseUser($GLOBALS['TL_CONFIG']['dbUser']);
$client->setDatabasePassword($GLOBALS['TL_CONFIG']['dbPass']);
$client->setDatabasePersistent($GLOBALS['TL_CONFIG']['dbPconnect']);
$client->setDatabaseCharset($GLOBALS['TL_CONFIG']['dbCharset']);

// set installation root path
$client->setInstallationRootPath(TL_ROOT);
