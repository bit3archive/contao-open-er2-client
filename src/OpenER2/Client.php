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

namespace OpenER2;

/**
 * Class OpenER2ClientAPI
 *
 * The Open ER2 Client API master class.
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class Client
{
	/**
	 * The ER2 wsdl url.
	 *
	 * @var string
	 */
	protected $wsdl = 'http://www.contao.org/services/repository.wsdl';

	/**
	 * The database dsn.
	 *
	 * @var string
	 */
	protected $databaseDsn = '';

	/**
	 * The database user.
	 *
	 * @var string
	 */
	protected $databaseUser = '';

	/**
	 * The database password.
	 *
	 * @var string
	 */
	protected $databasePassword = '';

	/**
	 * Use persistend database connection.
	 *
	 * @var string
	 */
	protected $databasePersistent = false;

	/**
	 * The database charset.
	 *
	 * @var string
	 */
	protected $databaseCharset = 'UTF8';

	/**
	 * Automatic update database schema.
	 *
	 * @var bool
	 */
	protected $databaseAutoUpdate = true;

	/**
	 * The installation root path.
	 *
	 * @var string
	 */
	protected $installationRootPath = '';

	/**
	 * Be compatible to original Contao ER2 client.
	 *
	 * @var bool
	 */
	protected $contaoER2Compat = true;

	/**
	 * Languages to synchronise.
	 *
	 * @var array
	 */
	protected $languages = array('en');

	/**
	 * The logger.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * The Soap Client instance.
	 *
	 * @var \SoapClient
	 */
	protected $soap = null;

	/**
	 * The database pdo instance.
	 *
	 * @var \PDO
	 */
	protected $database = null;

	/**
	 * The repository instance.
	 *
	 * @var Repository
	 */
	protected $repository = null;

	/**
	 *
	 */
	public function __construct()
	{
		$this->logger = new \Monolog\Logger('OpenER2Client');
	}

	/**
	 * @return \Monolog\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @param string $wsdl
	 */
	public function setWsdl($wsdl)
	{
		$this->wsdl = $wsdl;
	}

	/**
	 * @return string
	 */
	public function getWsdl()
	{
		return $this->wsdl;
	}

	/**
	 * @param string $databaseDsn
	 */
	public function setDatabaseDsn($databaseDsn)
	{
		$this->databaseDsn = $databaseDsn;
	}

	/**
	 * @return string
	 */
	public function getDatabaseDsn()
	{
		return $this->databaseDsn;
	}

	/**
	 * @param string $databaseUser
	 */
	public function setDatabaseUser($databaseUser)
	{
		$this->databaseUser = $databaseUser;
	}

	/**
	 * @return string
	 */
	public function getDatabaseUser()
	{
		return $this->databaseUser;
	}

	/**
	 * @param string $databasePassword
	 */
	public function setDatabasePassword($databasePassword)
	{
		$this->databasePassword = $databasePassword;
	}

	/**
	 * @return string
	 */
	public function getDatabasePassword()
	{
		return $this->databasePassword;
	}

	/**
	 * @param string $databasePersistent
	 */
	public function setDatabasePersistent($databasePersistent)
	{
		$this->databasePersistent = $databasePersistent;
	}

	/**
	 * @return string
	 */
	public function getDatabasePersistent()
	{
		return $this->databasePersistent;
	}

	/**
	 * @param string $databaseCharset
	 */
	public function setDatabaseCharset($databaseCharset)
	{
		$this->databaseCharset = $databaseCharset;
	}

	/**
	 * @return string
	 */
	public function getDatabaseCharset()
	{
		return $this->databaseCharset;
	}

	/**
	 * @param boolean $databaseAutoUpdate
	 */
	public function setDatabaseAutoUpdate($databaseAutoUpdate)
	{
		$this->databaseAutoUpdate = $databaseAutoUpdate;
	}

	/**
	 * @return boolean
	 */
	public function getDatabaseAutoUpdate()
	{
		return $this->databaseAutoUpdate;
	}

	/**
	 * @param string $installationRootPath
	 */
	public function setInstallationRootPath($installationRootPath)
	{
		$this->installationRootPath = $installationRootPath;
	}

	/**
	 * @return string
	 */
	public function getInstallationRootPath()
	{
		return $this->installationRootPath;
	}

	/**
	 * @param boolean $contaoER2Compat
	 */
	public function setContaoER2Compat($contaoER2Compat)
	{
		$this->contaoER2Compat = $contaoER2Compat;
	}

	/**
	 * @return boolean
	 */
	public function getContaoER2Compat()
	{
		return $this->contaoER2Compat;
	}

	/**
	 * @param array $languages
	 */
	public function setLanguages($languages)
	{
		$this->languages = $languages;
	}

	/**
	 * @return array
	 */
	public function getLanguages()
	{
		return $this->languages;
	}

	/**
	 * Get the er2 soap client.
	 *
	 * @return \SoapClient
	 */
	public function getSoap()
	{
		if ($this->soap == null)
		{
			$this->soap = new \SoapClient($this->wsdl);
		}

		return $this->soap;
	}

	/**
	 * @return \PDO
	 */
	public function getDatabase()
	{
		if ($this->database == null)
		{
			$this->database = new \PDO($this->databaseDsn,
				$this->databaseUser,
				$this->databasePassword,
				array(
					\PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_PERSISTENT => $this->databasePersistent
				));
			$this->database->exec('SET NAMES ' . $this->databaseCharset);
			$this->database->exec('SET CHARACTER SET ' . $this->databaseCharset);

			if ($this->databaseAutoUpdate)
			{
				$this->updateDatabaseSchema();
			}
		}

		return $this->database;
	}

	/**
	 * Update the database schema.
	 */
	public function updateDatabaseSchema()
	{
		$objDbInstaller = new DbInstaller($this);
		$arrSql = $objDbInstaller->compileCommands(array(__DIR__ . '/database.sql'), 'open_er2_');

		if (isset($arrSql['CREATE']))
		{
			foreach ($arrSql['CREATE'] as $strSql)
			{
				$this->logger->addDebug("Update database:\n" . $strSql);
				$this->database->exec($strSql);
			}
		}
		if (isset($arrSql['ALTER_ADD']))
		{
			foreach ($arrSql['ALTER_ADD'] as $strSql)
			{
				$this->logger->addDebug("Update database:\n" . $strSql);
				$this->database->exec($strSql);
			}
		}
		if (isset($arrSql['ALTER_CHANGE']))
		{
			foreach ($arrSql['ALTER_CHANGE'] as $strSql)
			{
				$this->logger->addDebug("Update database:\n" . $strSql);
				$this->database->exec($strSql);
			}
		}
	}

	/**
	 * Get the repository.
	 *
	 * @return Repository
	 */
	public function getRepository()
	{
		if ($this->repository == null)
		{
			$this->repository = new Repository($this);
		}

		return $this->repository;
	}
}
