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
	 * @var int
	 */
	protected $latestVersion = -1;

	/**
	 * Available versions of the extension.
	 *
	 * @var array
	 */
	protected $availableVersions = null;

	/**
	 * The installed version.
	 *
	 * @var int
	 */
	protected $installedVersion = -1;

	/**
	 * @param Client $client
	 * @param string $name
	 */
	public function __construct(Client $client, $name)
	{
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
	 * Set recent version.
	 *
	 * @param int $minVersion
	 * @param int $maxVersion
	 * @param bool $strict
	 * @return int
	 */
	public function getRecentVersion($minVersion = false, $maxVersion = false, $strict = false)
	{
		$this->getAvailableVersions();

		if ($minVersion !== false || $maxVersion != false)
		{
			if ($minVersion === false)
			{
				$minVersion = $this->availableVersions[count($this->availableVersions)-1];
			}
			if ($maxVersion === false)
			{
				$maxVersion = $this->availableVersions[0];
			}

			$versions = array_filter($this->availableVersions, function($version) use ($minVersion, $maxVersion, $strict) {
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
						$nextMajor = (($maxVersion / 10000000) + 1) * 10000000;
						if ($version >= $nextMajor) {
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

			$newerVersions = array_filter($this->availableVersions, function($version) use ($installedVersion) {
				return $version >= $installedVersion;
			});

			// find recent version, that is newer as or it is the installed version
			$this->recentVersion = $this->findRecentVersion($newerVersions);

			// no recent version found
			if ($this->recentVersion == 0)
			{
				// fallback, search recent version in all available versions
				$this->recentVersion = $this->findRecentVersion($this->availableVersions);
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
			return $version ^ $status == $status;
		});

		if (count($tmp)) {
			return $tmp[0];
		} else if ($status > 0) {
			return $this->findRecentVersion($versions, $status-1);
		} else {
			return 0;
		}
	}

	/**
	 * Get the latest version.
	 * 
	 * @return int
	 */
	public function getLatestVersion()
	{
		if ($this->latestVersion == -1)
		{
			$this->getAvailableVersions();
			$this->latestVersion = $this->availableVersions[0];
		}

		return $this->latestVersion;
	}

	/**
	 * Get available versions.
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
	 * Get the installed version string.
	 *
	 * @return null|string
	 */
	public function getInstalledVersion()
	{
		// TODO get installed version
		throw new Exception('Not yet implemented');
		return $this->installedVersion;
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

}
