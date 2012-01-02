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

namespace Contao\OpenER2\Exception;

/**
 * Class DuplicateDependencyException
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class DuplicateDependencyException extends Exception
{
	/**
	 * The parent dependency.
	 *
	 * @var \Contao\OpenER2\Dependency\Dependency
	 */
	protected $parentDependency;

	/**
	 * The duplicated dependency.
	 *
	 * @var \Contao\OpenER2\Dependency\Dependency
	 */
	protected $existingDependency;

	/**
	 * The duplicated dependency.
	 *
	 * @var \Contao\OpenER2\Dependency\Dependency
	 */
	protected $duplicateDependency;

	/**
	 * @param \Contao\OpenER2\Dependency\Dependency $parentDependency
	 * @param \Contao\OpenER2\Dependency\Dependency $existingDependency
	 * @param \Contao\OpenER2\Dependency\Dependency $duplicateDependency
	 */
	public function __construct(\Contao\OpenER2\Dependency\Dependency $parentDependency, \Contao\OpenER2\Dependency\Dependency $existingDependency, \Contao\OpenER2\Dependency\Dependency $duplicateDependency)
	{
		parent::__construct('Duplicate dependency');

		$this->parentDependency = $parentDependency;
		$this->existingDependency = $existingDependency;
		$this->duplicateDependency = $duplicateDependency;
	}

	/**
	 * @return \Contao\OpenER2\Dependency\Dependency
	 */
	public function getParentDependency()
	{
		return $this->parentDependency;
	}

	/**
	 * @return \Contao\OpenER2\Dependency\Dependency
	 */
	public function getExistingDependency()
	{
		return $this->existingDependency;
	}

	/**
	 * @return \Contao\OpenER2\Dependency\Dependency
	 */
	public function getDuplicateDependency()
	{
		return $this->duplicateDependency;
	}
}
