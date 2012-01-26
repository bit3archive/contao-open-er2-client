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

namespace Contao\OpenER2\Client;

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
	 * The logger.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * The database pdo instance.
	 *
	 * @var \PDO
	 */
	protected $database = null;

	/**
	 * The Soap Client instance.
	 *
	 * @var \SoapClient
	 */
	protected $soap = null;

	/**
	 * The installation root path.
	 *
	 * @var string
	 */
	protected $installationRootPath = '';

	/**
	 * The tl_files path.
	 *
	 * @var string
	 */
	protected $filesPath = 'tl_files';

	/**
	 * The current contao version.
	 *
	 * @var int
	 */
	protected $contaoVersion;

	/**
	 * Automatic update database schema.
	 *
	 * @var bool
	 */
	protected $databaseAutoUpdate = true;

	/**
	 * Database auto update is run.
	 *
	 * @var bool
	 */
	protected $databaseUpdated = false;

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
	 * The repository instance.
	 *
	 * @var Repository
	 */
	protected $repository = null;

	/**
	 * The license registry.
	 *
	 * @var LicenseRegistry
	 */
	protected $licenseRegistry;

	/**
	 * Static repository url.
	 *
	 * @var string
	 */
	protected $staticRepositoryUrl = 'http://contao.infinitysoft.de/open_er2/staticRepository.bin';

	/**
	 *
	 */
	public function __construct(\Monolog\Logger $logger, \PDO $database, \SoapClient $soap)
	{
		$this->logger = $logger;
		$this->database = $database;
		$this->soap = $soap;
		$this->installationRootPath = getcwd();
	}

	/**
	 * @return \Monolog\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @return \PDO
	 */
	public function getDatabase()
	{
		if ($this->databaseAutoUpdate && !$this->databaseUpdated)
		{
			$this->databaseAutoUpdate = false;
			$this->databaseUpdated = true;
			$this->updateDatabaseSchema();
		}

		return $this->database;
	}

	/**
	 * Get the er2 soap client.
	 *
	 * @return \SoapClient
	 */
	public function getSoap()
	{
		return $this->soap;
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
	 * @param string $filesPath
	 */
	public function setFilesPath($filesPath)
	{
		$this->filesPath = $filesPath;
	}

	/**
	 * @return string
	 */
	public function getFilesPath()
	{
		return $this->filesPath;
	}

	/**
	 * @param int $contaoVersion
	 */
	public function setContaoVersion($version)
	{
		if (is_numeric($version)) {
			$this->contaoVersion = $version;
			return true;
		}

		$this->contaoVersion = VersionHelper::parseVersion($version);
		if ($this->contaoVersion > 0) {
			return true;
		}

		$this->contaoVersion = 0;
		return false;
	}

	/**
	 * @return int
	 */
	public function getContaoVersion()
	{
		return $this->contaoVersion;
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
	 * @param boolean $contaoER2Compat
	 */
	public function setContaoER2Compat($contaoER2Compat)
	{
		$this->contaoER2Compat = $contaoER2Compat;
	}

	/**
	 * @return boolean
	 */
	public function isContaoER2Compat()
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

	/**
	 * Get the license registry.
	 *
	 * @return LicenseRegistry
	 */
	public function getLicenseRegistry()
	{
		if ($this->licenseRegistry == null)
		{
			$this->licenseRegistry = new LicenseRegistry($this);
		}

		return $this->licenseRegistry;
	}

	/**
	 * @param string $staticRepositoryUrl
	 */
	public function setStaticRepositoryUrl($staticRepositoryUrl)
	{
		$this->staticRepositoryUrl = $staticRepositoryUrl;
	}

	/**
	 * @return string
	 */
	public function getStaticRepositoryUrl()
	{
		return $this->staticRepositoryUrl;
	}
}
