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
 * Example file how to use in a custom environment.
 */

$client = new \OpenER2\Client();

// set wsdl url
$client->setWsdl('http://www.contao.org/services/repository.wsdl');

// set database settings
$client->setDatabaseDsn('mysql:dbname=contao;host=localhost');
$client->setDatabaseUser('username');
$client->setDatabasePassword('password');
$client->setDatabasePersistent(true);
$client->setDatabaseCharset('UTF8');

// set installation root path
$client->setInstallationRootPath('/my/path/to/contao');
