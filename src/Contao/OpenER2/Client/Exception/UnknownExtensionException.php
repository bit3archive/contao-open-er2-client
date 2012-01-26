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
 * Class UnknownExtensionException
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class UnknownExtensionException extends Exception
{
	/**
	 * The extension name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		parent::__construct('Could not find extension ' . $name . ' in the repository (did you sync your repository?).');
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}
