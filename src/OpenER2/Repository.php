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
class Repository
{
	/**
	 * The Client instance.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * @param Client $objClient
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Sync repository information.
	 */
	public function syncRepository()
	{
		$this->client->getLogger()->addInfo('Synchronise repository information');

		$objExtensionListArgs = new \stdClass();
		$objExtensionListArgs->sets  = 'history,dependencies';
		$objExtensionListArgs->limit = 1;
		$arrExtensions = $this->client->getSoap()
			->getExtensionList($objExtensionListArgs);


		foreach ($arrExtensions as $objExtension)
		{
			$strName = $objExtension->name;

			foreach ($objExtension->allversions as $objVersion)
			{
				$this->syncExtension($strName, $objVersion->version);
			}
		}

		var_dump($arrExtensions);

	}

	/**
	 * Sync extension informations.
	 *
	 * @param string $name
	 * @param int $version
	 * @param bool $force
	 */
	public function syncExtension($name, $version, $force = false)
	{
		if (!$force)
		{
			$stmt = $this->client->getDatabase()
				->prepare('SELECT * FROM open_er2_repository WHERE name=? AND version=?');
			$stmt->bindValue(1, $name);
			$stmt->bindValue(2, $version);
			$stmt->execute();
			if ($stmt->rowCount)
			{
				return;
			}
		}

		$this->client->getLogger()->addInfo('Synchronise extension ' . $name . ', ' . $version . ' information');

		$objExtensionListArgs           = new \stdClass();
		$objExtensionListArgs->sets     = 'history,dependencies';
		$objExtensionListArgs->names    = $name;
		$objExtensionListArgs->versions = $version;

		$arrExtensions = $this->client->getSoap()->getExtensionList($objExtensionListArgs);
		if (count($arrExtensions)) {
			$objExtension = $arrExtensions[0];

			$stmt = $this->client->getDatabase()
				->prepare("INSERT INTO open_er2_repository
						   SET name=:name, version=:version, build=:build, releasedate=:releasedate,
						       author=:author, authorName=:authorName, authorSite=:authorSite,
						       type=:type, category=:category,
						       coreMinVersion=:coreMinVersion, coreMaxVersion=:coreMaxVersion,
						       license=:license, language=:language, title=:title, teaser=:teaser");
		}
	}
}