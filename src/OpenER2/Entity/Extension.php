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

namespace OpenER2\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Extension
 *
 * The Extension model class.
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 *
 * @ORM\Entity
 * @ORM\Table(name="opener2_extension")
 */
class Extension
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=100)
	 * @var string
	 */
	protected $name = "";

	/**
	 * @ORM\Column(type="blob")
	 * @var array
	 */
	protected $availableVersions;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @var string
	 */
	protected $installedVersion = null;


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
