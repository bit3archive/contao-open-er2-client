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
	protected $recentVersion = 0;

	/**
	 * Latest version of the extension.
	 *
	 * @var int
	 */
	protected $latestVersion = 0;

	/**
	 * Available versions of the extension.
	 *
	 * @var array
	 */
	protected $availableVersions = array();

	/**
	 * The installed version.
	 *
	 * @var int
	 */
	protected $installedVersion = 0;

	/**
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Set the extension name.
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * Get recent version.
	 *
	 * @param int $recentVersion
	 */
	public function setRecentVersion($recentVersion)
	{
		$this->recentVersion = $recentVersion;
	}

	/**
	 * Set recent version.
	 *
	 * @return int
	 */
	public function getRecentVersion()
	{
		return $this->recentVersion;
	}

	/**
	 * Set the latest version.
	 *
	 * @param int $latestVersion
	 */
	public function setLatestVersion($latestVersion)
	{
		$this->latestVersion = $latestVersion;
	}

	/**
	 * Get the latest version.
	 * 
	 * @return int
	 */
	public function getLatestVersion()
	{
		return $this->latestVersion;
	}

	/**
	 * Set available versions.
	 *
	 * @param array $availableVersions
	 */
	public function setAvailableVersions($availableVersions)
	{
		$this->availableVersions = $availableVersions;
	}

	/**
	 * Get available versions.
	 *
	 * @return mixed
	 */
	public function getAvailableVersions()
	{
		return $this->availableVersions;
	}

	/**
	 * Get the installed version string.
	 *
	 * @return null|string
	 */
	public function getInstalledVersion()
	{
		return $this->installedVersion;
	}

	/**
	 * Check if this extension is installed.
	 *
	 * @return bool
	 */
	public function isInstalled()
	{
		return !empty($this->installedVersion);
	}

}
