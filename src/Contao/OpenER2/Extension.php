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

namespace Contao\OpenER2;

/**
 * Class Extension
 *
 * The Extension model class.
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class Extension
{
	/**
	 * The client instance.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Name of the extension.
	 *
	 * @var string
	 */
	protected $name = "";

	/**
	 * Recent version of the extension.
	 *
	 * @var int
	 */
	protected $recentVersion = -1;

	/**
	 * Latest version of the extension.
	 *
	 * @var array
	 */
	protected $latestVersion = array();

	/**
	 * Available versions of the extension.
	 *
	 * @var array
	 */
	protected $availableVersions = null;

	/**
	 * Compatible versions of the extension.
	 *
	 * @var array
	 */
	protected $compatibleVersions = array(true=>null, false=>null);

	/**
	 * The installed version.
	 *
	 * @var int
	 */
	protected $installedVersion = -1;

	/**
	 * The installed build.
	 *
	 * @var int
	 */
	protected $installedBuild = -1;

	/**
	 * The dependency graph.
	 *
	 * @var array
	 */
	protected $dependencyGraph = null;

	/**
	 * @param Client $client
	 * @param string $name
	 */
	public function __construct(Client $client, $name)
	{
		$stmt = $client->getDatabase()->prepare('SELECT * FROM open_er2_repository WHERE name=?');
		$stmt->bindValue(1, $name);
		$stmt->execute();
		if ($stmt->rowCount() == 0) {
			throw new \Contao\OpenER2\Exception\UnknownExtensionException($name);
		}

		$this->client = $client;
		$this->name   = $name;
	}

	/**
	 * Get the extension name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get latest recent (stable) version.
	 *
	 * @param int $minVersion
	 * @param int $maxVersion
	 * @param bool $strict
	 * @param bool $upstream
	 * @return int
	 */
	public function getRecentVersion($minVersion = false, $maxVersion = false, $strict = false, $upstream = false)
	{
		$versions = $upstream ? $this->getAvailableVersions() : $this->getCompatibleVersions($strict);

		if ($minVersion !== false || $maxVersion !== false || $strict !== false || $upstream !== false)
		{
			if ($minVersion === false)
			{
				$minVersion = count($versions) ? $versions[count($versions)-1] : PHP_INT_SIZE;
			}
			if ($maxVersion === false)
			{
				$maxVersion = count($versions) ? $versions[0] : 0;
			}

			$versions = array_filter($versions, function($version) use ($minVersion, $maxVersion, $strict) {
				// version is too old
				if ($version < $minVersion) {
					return false;
				}
				// version is too new
				if ($version > $maxVersion) {
					// if strict, filter out
					if ($strict) {
						return false;
					} else {
						// if the major change, filter out
						$maxVersion = VersionHelper::calculateRecentMajorVersion($maxVersion);
						if ($version > $maxVersion) {
							return false;
						}
					}
				}
				return true;
			});

			return $this->findRecentVersion($versions);
		}

		if ($this->recentVersion == -1)
		{
			$installedVersion = $this->getInstalledVersion();

			$newerVersions = array_filter($versions, function($version) use ($installedVersion) {
				return $version >= $installedVersion;
			});

			// find recent version, that is newer as or it is the installed version
			$this->recentVersion = $this->findRecentVersion($newerVersions);

			// no recent version found
			if ($this->recentVersion == 0)
			{
				// fallback, search recent version in all available versions
				$this->recentVersion = $this->findRecentVersion($versions);
			}
		}

		return $this->recentVersion;
	}

	/**
	 * Find recent version.
	 *
	 * @param array $versions
	 * @param int $status
	 */
	protected function findRecentVersion($versions, $status = 9)
	{
		$tmp = array_filter($versions, function($version) use ($status) {
			return $version & $status == $status;
		});

		if (count($tmp)) {
			return array_shift($tmp);
		} else if ($status > 0) {
			return $this->findRecentVersion($versions, $status-1);
		} else {
			return 0;
		}
	}

	/**
	 * Get the latest version.
	 *
	 * @param bool $strict
	 * @param bool $upstream
	 * @return int
	 */
	public function getLatestVersion($strict = false, $upstream = false)
	{
		$k = ($strict ? 1 : 0) + ($upstream ? 2 : 0);

		if (!isset($this->latestVersion[$k]))
		{
			$versions = $upstream ? $this->getAvailableVersions() : $this->getCompatibleVersions($strict);
			$this->latestVersion[$k] = count($versions) ? $versions[0] : 0;
		}

		return $this->latestVersion[$k];
	}

	/**
	 * Get all available versions.
	 *
	 * @return mixed
	 */
	public function getAvailableVersions()
	{
		if ($this->availableVersions == null)
		{
			$stmt = $this->client->getDatabase()
				->prepare("SELECT DISTINCT version FROM open_er2_repository WHERE name=? ORDER BY version DESC");
			$stmt->bindValue(1, $this->name);
			$stmt->execute();

			$this->availableVersions = $stmt->fetchAll(\PDO::FETCH_COLUMN);
			rsort($this->availableVersions);
		}

		return $this->availableVersions;
	}

	/**
	 * Get all versions that are compatible to the current contao version.
	 *
	 * @param bool $strict
	 * @return mixed
	 */
	public function getCompatibleVersions($strict = false)
	{
		if ($this->compatibleVersions[$strict] == null)
		{
			$stmt = $this->client->getDatabase()
				->prepare("SELECT DISTINCT version
						   FROM open_er2_repository
						   WHERE name=:name
						   AND coreMinVersion<=:contaoVersion
						   AND " . ($strict ? "coreMaxVersion" : "FLOOR(coreMaxVersion/10000)*10000+9999") . ">=:contaoVersion
						   ORDER BY version DESC");
			$stmt->bindValue('name', $this->name);
			$stmt->bindValue('contaoVersion', $this->client->getContaoVersion());
			$stmt->execute();

			$this->compatibleVersions[$strict] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
			rsort($this->compatibleVersions[$strict]);
		}

		return $this->compatibleVersions[$strict];
	}

	/**
	 * Get the installed version number.
	 *
	 * @return int
	 */
	public function getInstalledVersion()
	{
		if ($this->installedVersion == -1)
		{
			$stmt = $this->client->getDatabase()
				->prepare("SELECT version, build FROM open_er2_installed_extensions WHERE extension=?");
			$stmt->bindValue(1, $this->name);
			$stmt->execute();

			// if installed, set the version and build number.
			if ($stmt->rowCount()) {
				$objExtension = $stmt->fetch(\PDO::FETCH_OBJ);

				$this->installedVersion = (int)$objExtension->version;
				$this->installedBuild   = (int)$objExtension->build;
			}

			// (compat mode) if not installed, check in contao er2 table
			else if ($this->client->isContaoER2Compat()) {
				$stmt = $this->client->getDatabase()
					->prepare("SELECT * FROM tl_repository_installs WHERE extension=?");
				$stmt->bindValue(1, $this->name);
				$stmt->execute();

				// extension is installed by contao er2 client
				if ($stmt->rowCount()) {
					$objExtension = $stmt->fetch(\PDO::FETCH_OBJ);

					// register extension in open er2 table
					$stmt = $this->client->getDatabase()
						->prepare("INSERT INTO open_er2_installed_extensions
								   SET extension=:extension, version=:version, build=:build,
								   isDependency=:isDependency, installed=:installed, updated=:updated,
								   allowedStatus=:allowedStatus,
								   doNotDelete=:doNotDelete, doNotUpdate=:doNotUpdate,
								   hasErrors=:hasErrors");
					$stmt->bindValue('extension', $this->name);
					$stmt->bindValue('version', $objExtension->version);
					$stmt->bindValue('build', $objExtension->build);
					$stmt->bindValue('isDependency', '');
					$stmt->bindValue('installed', date('Y-m-d H:i:s', $objExtension->tstamp));
					$stmt->bindValue('updated', date('Y-m-d H:i:s', $objExtension->tstamp));
					$stmt->bindValue('allowedStatus', $objExtension->alpha ? 0 : ($objExtension->beta ? 3 : ($objExtension->rc ? 6 : 9)));
					$stmt->bindValue('doNotDelete', $objExtension->delprot);
					$stmt->bindValue('doNotUpdate', $objExtension->updprot);
					$stmt->bindValue('hasErrors', $objExtension->error);
					$stmt->execute();

					// if exists, set the license key
					if ($objExtension->lickey) {
						$this->client->getLicenseRegistry()->setLicense($this->name, $objExtension->lickey);
					}

					$this->installedVersion = (int)$objExtension->version;
					$this->installedBuild   = (int)$objExtension->build;
				}

				// extension is not installed
				else {
					$this->installedVersion = 0;
					$this->installedBuild   = 0;
				}
			}

			// extension is not installed
			else {
				$this->installedVersion = 0;
				$this->installedBuild   = 0;
			}
		}

		return $this->installedVersion;
	}

	/**
	 * Get the installed build number.
	 */
	public function getInstalledBuild()
	{
		if ($this->installedBuild == -1)
		{
			$this->getInstalledVersion();
		}

		return $this->installedBuild;
	}

	/**
	 * Check if this extension is installed.
	 *
	 * @return bool
	 */
	public function isInstalled()
	{
		return $this->getInstalledVersion() != 0;
	}

	/**
	 * Get the dependency graph.
	 *
	 * @param int $version
	 * @param bool $strict
	 * @return \Contao\OpenER2\Dependency\DependencyGraph
	 */
	public function getDependencyGraph($version = false, $strict = false)
	{
		if (!$version) {
			$version = $this->getInstalledVersion();

			if (!$version) {
				$version = $this->getRecentVersion();
			}
		}

		if (!isset($this->dependencyGraph[$version]))
		{
			$tmp = array();
			$this->dependencyGraph[$version] = new \Contao\OpenER2\Dependency\DependencyGraph($this, $version, $version);
			$this->calculateDependencyGraph($this->dependencyGraph[$version], $this->dependencyGraph[$version], $version, $strict, $tmp);
		}

		return $this->dependencyGraph[$version];
	}

	/**
	 * Calculate the dependency graph.
	 *
	 * @param array dependencies
	 * @return \Contao\OpenER2\Dependency\DependencyGraph
	 */
	protected function calculateDependencyGraph(\Contao\OpenER2\Dependency\DependencyGraph $graph,
	                                            \Contao\OpenER2\Dependency\Dependency $thisDependency,
	                                            $version,
	                                            $strict)
	{
		$stmt = $this->client->getDatabase()
			->prepare("SELECT * FROM open_er2_repository_dependency WHERE extension=? AND version=?");
		$stmt->bindValue(1, $this->name);
		$stmt->bindValue(2, $version);
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$stmt->execute();
		$list = $stmt->fetchAll();

		// calculate this extension dependencies
		foreach ($list as $dependency)
		{
			if (!$strict) {
				$dependency->maxVersion = VersionHelper::calculateRecentMajorVersion($dependency->maxVersion);
			}

			$dependsOn = $this->client->getRepository()->getExtension($dependency->dependsOn);
			$dep = $graph->dependsOn($dependsOn);

			if ($dep)
			{
				if (   $dep->getMinVersion() > $dependency->maxVersion
					&& $dep->getMaxVersion() < $dependency->minVersion)
				{
					$exception = new Exception\UnresolveableDependencyException($dependency, $dep);

					// log the unresolvable exception
					$this->client->getLogger()
						->addError($exception->getMessage());

					throw $exception;
				}
				else
				{
					// recalculate the min version
					$dep->setMinVersion(max(
						$dep->getMinVersion(),
						$dependency->minVersion
					));
					// recalculate the max version
					$dep->setMaxVersion(min(
						$dep->getMaxVersion(),
						$dependency->maxVersion
					));
				}
			}
			else
			{
				$dep = new \Contao\OpenER2\Dependency\Dependency($dependsOn, $dependency->minVersion, $dependency->maxVersion);
			}

			$thisDependency->addDependency($dep);

			// append dependencies from dependencies
			$dependsOn->calculateDependencyGraph(
				$graph,
				$dep,
				$dependsOn->getInstalledVersion() ? $dependsOn->getInstalledVersion() : $dependsOn->getRecentVersion(),
				$strict
			);
		}

		// search for installed extensions and there dependencies
		$stmt = $this->client->getDatabase()
			->prepare("SELECT d.* FROM open_er2_repository_dependency d
					   INNER JOIN open_er2_installed_extensions e ON d.extension=e.extension AND d.version=e.version");
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$stmt->execute();
		foreach ($stmt as $dependency)
		{
			if (!$strict) {
				$dependency->maxVersion = VersionHelper::calculateRecentMajorVersion($dependency->maxVersion);
			}

			$dependsOn = $this->client->getRepository()->getExtension($dependency->dependsOn);
			$dep = $graph->dependsOn($dependsOn);

			if ($dep)
			{
				if (   $dep->getMinVersion() > $dependency->maxVersion
					&& $dep->getMaxVersion() < $dependency->minVersion)
				{
					$exception = new Exception\UnresolveableDependencyException($dependency, $dep);

					// log the unresolvable exception
					$this->client->getLogger()
						->addError($exception->getMessage());

					throw $exception;
				}
				else
				{
					// recalculate the min version
					$dep->setMinVersion(max(
						$dep->getMinVersion(),
						$dependency->minVersion
					));
					// recalculate the max version
					$dep->setMaxVersion(min(
						$dep->getMaxVersion(),
						$dependency->maxVersion
					));
				}
			}
		}
	}

	/**
	 * Install the extension.
	 *
	 * @return bool
	 * @throws Exception\ExtensionAllreadyInstalledException
	 */
	public function install($version = false, $isDependency = false, $strict = false)
	{
		if ($this->isInstalled()) {
			throw new Exception\ExtensionAllreadyInstalledException($this->name);
		}

		return $this->installOrUpdate($version, $isDependency, $strict);
	}

	/**
	 * Install the extension.
	 *
	 * @return bool
	 * @throws Exception\ExtensionAllreadyInstalledException
	 */
	public function installWithDependencies($version = false, $strict = false)
	{
		if ($this->isInstalled()) {
			throw new Exception\ExtensionAllreadyInstalledException($this->name);
		}

		if ($this->install($version, false, $strict))
		{
			$dependencies = $this->getDependencyList($version);
			foreach ($dependencies as $k=>$v)
			{
				$extension = $this->client->getRepository()
					->getExtension($k);
				$depVersion = $extension->getRecentVersion($v->min, $v->max);
				$extension->installOrUpdate($depVersion, false, true);
			}

			return true;
		}
		return false;
	}

	/**
	 * Update the extension.
	 *
	 * @return bool
	 * @throws Exception\ExtensionNotInstalledException
	 */
	public function update($version = false, $strict = false)
	{
		if (!$this->isInstalled()) {
			throw new Exception\ExtensionNotInstalledException($this->name);
		}

		return $this->installOrUpdate($version, false, $strict);
	}

	public function installOrUpdate($version = false, $isDependency = false, $strict = false)
	{
		if ($version == false) {
			$version = $this->getRecentVersion(false, false, $strict);
		}

		$extension = $this->client->getRepository()
			->syncExtension($this->name, $version, true);

		$this->client->getLogger()
			->addInfo('Install extension ' . $this->name . ', ' . $version . ', ' . $extension->build);

		$licenseKey = $this->client->getLicenseRegistry()->getLicense($this->name);

		// insert into database
		// mark as has errors, if fetching package fails
		$arrParams = array
		(
			'extension'      => $this->name,
			'version'        => $version,
			'build'          => $extension->build,
			'updated'        => time(),
			'allowedStatus'  => 9,
			'hasErrors'      => '1'
		);
		$setUpdate = implode(',', array_map(function($strField) {
			return $strField . ' = :' . $strField;
		}, array_keys($arrParams)));

		$arrParams['installed']    = time();
		$arrParams['isDependency'] = $isDependency ? '1' : '';
		$setInsert = implode(',', array_map(function($strField) {
			return $strField . ' = :' . $strField;
		}, array_keys($arrParams)));

		$this->client->getDatabase()
			->prepare("INSERT INTO open_er2_installed_extensions
					   SET $setInsert
					   ON DUPLICATE KEY UPDATE $setUpdate")
			->execute($arrParams);

		// compatibility mode
		// update tl_repository_installs table
		if ($this->client->isContaoER2Compat())
		{
			$stmt = $this->client->getDatabase()
				->prepare("SELECT id FROM tl_repository_installs WHERE extension=?");
			$stmt->bindValue(1, $this->name);
			$stmt->execute();

			$arrParams = array
			(
				'tstamp'         => time(),
				'extension'      => $this->name,
				'version'        => $version,
				'build'          => $extension->build,
				'alpha'          => '',
				'beta'           => '',
				'rc'             => '',
				'stable'         => '',
				'lickey'         => $licenseKey ? $licenseKey : '',
				'error'          => '1'
			);
			$set = implode(',', array_map(function($strField) {
				return $strField . ' = :' . $strField;
			}, array_keys($arrParams)));

			if ($stmt->rowCount()) {
				$contaoER2Id = $arrParams['id'] = $stmt->fetchColumn(1);
				$this->client->getDatabase()
					->prepare("UPDATE tl_repository_installs SET $set WHERE id=:id")
					->execute($arrParams);
			} else {
				$this->client->getDatabase()
					->prepare("INSERT INTO tl_repository_installs SET $set")
					->execute($arrParams);
				$contaoER2Id = $this->client->getDatabase()
					->lastInsertId();
			}
		}

		// fetch the package url
		$request = new \stdClass();
		$request->name = $this->name;
		$request->version = $version;
		$request->build = $extension->build;
		if ($licenseKey) {
			$request->key = $licenseKey;
		}
		//$request->mode = 'install';
		$response = $this->client->getSoap()
			->getPackage($request);

		$request = new \Contao\OpenER2\HttpRequestExtended\RequestExtended();
		if (!$request->getUrlEncoded($response->url)) {
			$this->client->getLogger()
				->addError('Could not fetch package for ' . $this->name . ', ' . $version . ', ' . $extension->build . ' from ' . $response->url);
			throw new Exception('Could not fetch package.');
		}

		// compatibility mode
		// truncate tl_repository_installs table
		if ($this->client->isContaoER2Compat())
		{
			$this->client->getDatabase()
				->prepare("DELETE FROM tl_repository_instfiles WHERE pid=?")
				->execute(array($contaoER2Id));
		}

		$file = tempnam(sys_get_temp_dir(), 'package_');
		file_put_contents($file, $request->response);

		$zip = new \ZipArchive();
		$zip->open($file);

		for ($i=0; $i<$zip->numFiles; $i++)
		{
			$name = $zip->getNameIndex($i);

			if (preg_match('#^TL_ROOT/#', $name)) {
				$path = substr($name, 8);
			} else if (preg_match('#^TL_FILES/#', $name)) {
				$path = $this->client->getTlFilesPath() . '/' . substr($name, 9);
			} else {
				continue;
			}

			// generate absolute target path
			$target = $this->client->getInstallationRootPath() . '/' . $path;

			// create parent directories
			if (!is_dir(dirname($target)))
			{
				mkdir(dirname($target), 0777, true);
			}

			// load file content from zip
			$content = $zip->getFromIndex($i);

			// write file
			file_put_contents($target, $content);

			// calculate file hash
			$hash = md5_file($target);

			// store file information into database
			$arrParams = array
			(
				'extension'      => $this->name,
				'version'        => $version,
				'build'          => $extension->build,
				'file'           => $path,
				'checksum'       => $hash
			);
			$set = implode(',', array_map(function($strField) {
				return $strField . ' = :' . $strField;
			}, array_keys($arrParams)));

			$this->client->getDatabase()
				->prepare("INSERT INTO open_er2_installed_files
						   SET $set
						   ON DUPLICATE KEY UPDATE $set")
				->execute($arrParams);

			// compatibility mode
			// update tl_repository_installs table
			if ($this->client->isContaoER2Compat())
			{
				$arrParams = array
				(
					'pid'            => $contaoER2Id,
					'tstamp'         => time(),
					'filename'       => $path,
					'filetype'       => 'F',
					'flag'           => ''
				);
				$set = implode(',', array_map(function($strField) {
					return $strField . ' = :' . $strField;
				}, array_keys($arrParams)));

				$this->client->getDatabase()
					->prepare("INSERT INTO tl_repository_instfiles
							   SET $set")
					->execute($arrParams);
			}

			// log
			$this->client->getLogger()
				->addInfo('Install file ' . $hash . ' ' . $path);

			// clean
			unset($content);
		}

		// clean up waste files
		$stmt = $this->client->getDatabase()
			->prepare("SELECT * FROM open_er2_installed_files WHERE extension=? AND version!=? AND build!=?");
		$stmt->bindValue(1, $this->name);
		$stmt->bindValue(2, $version);
		$stmt->bindValue(3, $extension->build);
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$stmt->execute();
		foreach ($stmt as $file)
		{
			$this->client->getLogger()
				->addInfo('Remove waste file ' . $file->file);
			$path = $this->client->getInstallationRootPath() . '/' . $file->file;
			if (file_exists($path)) {
				unlink($path);
			}
		}
		$stmt = $this->client->getDatabase()
			->prepare("DELETE FROM open_er2_installed_files WHERE extension=? AND version!=? AND build!=?");
		$stmt->bindValue(1, $this->name);
		$stmt->bindValue(2, $version);
		$stmt->bindValue(3, $extension->build);
		$stmt->execute();

		// remove hasErrors flag
		$this->client->getDatabase()
			->prepare("UPDATE open_er2_installed_extensions SET hasErrors=? WHERE extension=?")
			->execute(array('', $this->name));

		if ($this->client->isContaoER2Compat())
		{
			$this->client->getDatabase()
				->prepare("UPDATE tl_repository_installs SET error=? WHERE extension=?")
				->execute(array('', $this->name));
		}

		return true;
	}
}
