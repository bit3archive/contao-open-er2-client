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
 * Class LicenseRegistry
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class LicenseRegistry
{
	/**
	 * @var Client
	 */
	protected $client;

	public function __construct(Client $client) {
		$this->client = $client;
	}

	/**
	 * Get the current registered license key for an extension.
	 *
	 * @param string $name
	 * @return bool|string
	 */
	public function getLicense($name)
	{
		$stmt = $this->client->getDatabase()
			->prepare("SELECT license FROM open_er2_license WHERE extension=?");
		$stmt->bindValue(1, $name);
		$stmt->execute();

		if ($stmt->rowCount()) {
			return $stmt->fetchColumn(1);
		} else {
			return false;
		}
	}

	/**
	 * Set the license key for an extension.
	 *
	 * @param string $name
	 * @param string $license
	 */
	public function setLicense($name, $license)
	{
		$stmt = $this->client->getDatabase()
			->prepare("INSERT INTO open_er2_license SET extension=:name, license=:license
					   ON DUPLICATE KEY UPDATE license=:license");
		$stmt->bindValue('name', $name);
		$stmt->bindValue('license', $license);
		$stmt->execute();
	}

	/**
	 * Remote the license key for an extension.
	 *
	 * @param string $name
	 */
	public function removeLicense($name)
	{
		$stmt = $this->client->getDatabase()
			->prepare("DELETE FROM open_er2_license WHERE extension=?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
	}
}
