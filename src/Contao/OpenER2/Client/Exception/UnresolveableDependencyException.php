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
 * Class UnresolveableDependencyException
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class UnresolveableDependencyException extends Exception
{
	/**
	 * The unresolved dependency.
	 *
	 * @var stdObject
	 */
	protected $unresolvedDependency;

	/**
	 * The current dependency.
	 *
	 * @var \Contao\OpenER2\Client\Dependency\Dependency
	 */
	protected $currentDependency;

	/**
	 * @param stdObject $dependency
	 * @param \Contao\OpenER2\Client\Dependency\Dependency $current
	 */
	public function __construct(\stdClass $unresolvedDependency, \Contao\OpenER2\Client\Dependency\Dependency $currentDependency)
	{
		parent::__construct('Could not resolve dependency graph. '
			. 'Searching for ' . $unresolvedDependency->dependsOn . ' from ' . $unresolvedDependency->minVersion . ' to ' . $unresolvedDependency->maxVersion . ', '
			. 'but require ' . $unresolvedDependency->dependsOn . ' from ' . $currentDependency->getMinVersion() . ' to ' . $currentDependency->getMaxVersion() . ' via ' . implode(', ', array_filter($currentDependency->getParents(), function (\Contao\OpenER2\Client\Dependency\Dependency $dependency) {
			return $dependency->getExtension()->getName();
		})));
		$this->unresolvedDependency = $unresolvedDependency;
		$this->currentDependency = $currentDependency;
	}

	/**
	 * @return \Contao\OpenER2\Client\Exception\stdObject
	 */
	public function getUnresolvedDependency()
	{
		return $this->unresolvedDependency;
	}

	/**
	 * @return \Contao\OpenER2\Client\Dependency\Dependency
	 */
	public function getCurrentDependency()
	{
		return $this->currentDependency;
	}
}
