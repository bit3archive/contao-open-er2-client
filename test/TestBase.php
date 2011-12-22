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

// display all errors
error_reporting(E_ALL);

// add a primitive autoloader
spl_autoload_register(function($name)
{
	$arrParts = explode('\\', $name);

	if ($arrParts[0] == 'Monolog') {
		$strFile = __DIR__ . '/../deps/monolog/src/' . implode('/', $arrParts) . '.php';
	} else {
		$strFile = __DIR__ . '/../src/' . implode('/', $arrParts) . '.php';
	}

	if (!file_exists($strFile)) {
		echo "Could not autoload $name\nfile $strFile does not exists!\n";
		debug_print_backtrace();
		exit(1);
	}
	require_once($strFile);
});

// set strict error handler
set_error_handler(function($errno, $errstr , $errfile = '', $errline = -1, $errcontext = null) {
	$logger = new \Monolog\Logger('PHP');
	$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stderr'));
	$logger->addCritical($errstr . ' in ' . $errfile . ':' . $errline);
	exit(1);
}, E_ALL);

// set strict exception handler
set_exception_handler(function(Exception $exception) {
	$logger = new \Monolog\Logger('PHP');
	$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stderr'));
	$logger->addCritical($exception->getMessage());
	$logger->addCritical($exception->getTraceAsString());
	exit(1);
});
