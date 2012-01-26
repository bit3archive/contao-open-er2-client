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

namespace Contao\OpenER2\Client\Exception;

/**
 * Class ExtensionAllreadyInstalledException
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class FetchPackageException extends \Exception
{
	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var int
	 */
	protected $version;

	/**
	 * @var build
	 */
	protected $build;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @param string $extension
	 */
	public function __construct($extension, $version, $build, $url)
	{
		parent::__construct(sprintf('The package for extension "%s" %s.%s could not be fetched from "%s"!', $extension, $url));
		$this->extension = $extension;
		$this->version = $version;
		$this->build = $build;
		$this->url = $url;
	}

	/**
	 * Get the extension name.
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}
}
