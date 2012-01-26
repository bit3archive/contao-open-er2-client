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
	 * Get an extension.
	 *
	 * @param string $name
	 * @return Extension
	 */
	public function getExtension($name)
	{
		return new Extension($this->client, $name);
	}

	/**
	 * Download and import static repository dump.
	 */
	public function importStaticRepository()
	{
		$url = $this->client->getStaticRepositoryUrl();
		$request = new \Contao\OpenER2\Client\HttpRequestExtended\RequestExtended();
		if (!$request->getUrlEncoded($url)) {
			throw new \Exception('Could not fetch static repository data.');
		}
		$list = unserialize($request->response);

		$this->client->getDatabase()
			->exec('TRUNCATE open_er2_repository');
		$this->client->getDatabase()
			->exec('TRUNCATE open_er2_repository_dependency');

		foreach ($list['extensions'] as $extension)
		{
			$arrParams = array
			(
				'name'           => $extension->name,
				'version'        => $extension->version,
				'build'          => $extension->build,
				'releaseDate'    => $extension->releasedate,
				'author'         => $extension->author,
				'authorName'     => $extension->authorname,
				'authorSite'     => $extension->authorsite,
				'type'           => $extension->type,
				'category'       => $extension->category,
				'coreMinVersion' => $extension->coreminversion,
				'coreMaxVersion' => $extension->coremaxversion,
				'language'       => $extension->language,
				'title'          => $extension->title,
				'teaser'         => $extension->teaser,
				'description'    => $extension->description,
				'releaseNotes'   => $extension->releasenotes,
				'license'        => $extension->license,
				'copyright'      => $extension->copyright
			);
			$set = implode(',', array_map(function($strField) {
				return $strField . ' = :' . $strField;
			}, array_keys($arrParams)));

			$stmt = $this->client->getDatabase()
				->prepare("INSERT INTO open_er2_repository
						   SET $set
						   ON DUPLICATE KEY UPDATE $set");
			$stmt->execute($arrParams);
		}

		foreach ($list['dependencies'] as $dependency)
		{
			$arrParams = array
			(
				'extension'  => $dependency->extension,
				'dependsOn'  => $dependency->dependsOn,
				'minVersion' => $dependency->minVersion,
				'maxVersion' => $dependency->maxVersion
			);
			$set = implode(',', array_map(function($strField) {
				return $strField . ' = :' . $strField;
			}, array_keys($arrParams)));

			$stmt = $this->client->getDatabase()
				->prepare("INSERT INTO open_er2_repository_dependency
						   SET $set
						   ON DUPLICATE KEY UPDATE $set");
			$stmt->execute($arrParams);
		}
	}

	/**
	 * Sync repository information.
	 */
	public function syncRepository($force = false)
	{
		$this->client->getLogger()->addInfo('Synchronise repository information');

		$objExtensionListArgs = new \stdClass();
		$objExtensionListArgs->sets  = 'history,dependencies';
		$arrExtensions = $this->client->getSoap()
			->getExtensionList($objExtensionListArgs);


		foreach ($arrExtensions as $objExtension)
		{
			$strName = $objExtension->name;

			foreach ($objExtension->allversions as $objVersion)
			{
				$this->syncExtension($strName, $objVersion->version, $force);
			}
		}
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
		$result = null;

		$db = $this->client->getDatabase();

		foreach ($this->client->getLanguages() as $language)
		{
			if (!$force)
			{
				$stmt = $this->client->getDatabase()
					->prepare('SELECT name, version, build FROM open_er2_repository WHERE name=? AND version=? AND language=?');
				$stmt->bindValue(1, $name);
				$stmt->bindValue(2, $version);
				$stmt->bindValue(3, $language);
				$stmt->execute();
				if ($stmt->rowCount())
				{
					$result = $stmt->fetch(\PDO::FETCH_OBJ);
					continue;
				}
			}

			$this->client->getLogger()->addInfo('Synchronise extension ' . $name . ', ' . $version . ', ' . $language . ' information');

			$objExtensionListArgs            = new \stdClass();
			$objExtensionListArgs->sets      = 'history,dependencies,details';
			$objExtensionListArgs->names     = $name;
			$objExtensionListArgs->versions  = $version;
			$objExtensionListArgs->languages = $language;

			$arrExtensions = $this->client->getSoap()->getExtensionList($objExtensionListArgs);
			if (count($arrExtensions)) {
				$objExtension = $arrExtensions[0];

				$arrParams = array
				(
					'name'           => $objExtension->name,
					'version'        => $objExtension->version,
					'build'          => $objExtension->build,
					'releaseDate'    => $objExtension->releasedate,
					'author'         => isset($objExtension->author) ? $objExtension->author : '',
					'authorName'     => isset($objExtension->authorname) ? $objExtension->authorname : '',
					'authorSite'     => isset($objExtension->authorsite) ? $objExtension->authorsite : '',
					'type'           => $objExtension->type,
					'category'       => $objExtension->category,
					'coreMinVersion' => $objExtension->coreminversion,
					'coreMaxVersion' => $objExtension->coremaxversion,
					'language'       => $objExtension->language,
					'title'          => isset($objExtension->title) ? $objExtension->title : '',
					'teaser'         => isset($objExtension->teaser) ? $objExtension->teaser : '',
					'description'    => isset($objExtension->description) ? $objExtension->description : '',
					'releaseNotes'   => isset($objExtension->releasenotes) ? $objExtension->releasenotes : '',
					'license'        => isset($objExtension->license) ? $objExtension->license : '',
					'copyright'      => isset($objExtension->copyright) ? $objExtension->copyright : ''
				);
				$set = implode(',', array_map(function($strField) {
					return $strField . ' = :' . $strField;
				}, array_keys($arrParams)));

				$stmt = $this->client->getDatabase()
					->prepare("INSERT INTO open_er2_repository
							   SET $set
							   ON DUPLICATE KEY UPDATE $set");
				$stmt->execute($arrParams);

				if (isset($objExtension->dependencies))
				{
					// remove deleted dependencies
					$deps = implode(',', array_map(function($dependency) use ($db) {
						return $db->quote($dependency->extension);
					}, $objExtension->dependencies));
					$stmt = $db->prepare('DELETE FROM open_er2_repository_dependency WHERE extension=? AND version=? AND dependsOn NOT IN (' . $deps . ')');
					$stmt->bindValue(1, $name);
					$stmt->bindValue(2, $version);
					$stmt->execute();

					// add or update dapendencies
					foreach ($objExtension->dependencies as $dependency)
					{
						$arrParams = array
						(
							'extension'  => $name,
							'version'    => $version,
							'dependsOn'  => $dependency->extension,
							'minVersion' => $dependency->minversion,
							'maxVersion' => $dependency->maxversion
						);
						$set = implode(',', array_map(function($strField) {
							return $strField . ' = :' . $strField;
						}, array_keys($arrParams)));

						$stmt = $this->client->getDatabase()
							->prepare("INSERT INTO open_er2_repository_dependency
									   SET $set
									   ON DUPLICATE KEY UPDATE $set");
						$stmt->execute($arrParams);
					}
				}

				$result = new \stdClass();
				$result->name    = $objExtension->name;
				$result->version = $objExtension->version;
				$result->build   = $objExtension->build;
			} else {
				$this->client->getLogger()->addWarning('Extension ' . $name . ', ' . $version . ', ' . $language . ' was not found in the repository!');
			}
		}

		return $result;
	}
}