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
	 * @var stdObject
	 */
	protected $currentDependency;

	/**
	 * @param stdObject $dependency
	 * @param stdObject $current
	 */
	public function __construct($unresolvedDependency, $currentDependency)
	{
		$message = 'Could not resolve dependency graph. '
				 . 'Searching for ' . $unresolvedDependency->dependsOn . ' from ' . $unresolvedDependency->minVersion . ' to ' . $unresolvedDependency->maxVersion . ', '
				 . 'but require ' . $unresolvedDependency->dependsOn . ' from ' . $currentDependency->min . ' to ' . $currentDependency->max . ' via ' . implode(', ', $currentDependency->via);
		parent::__construct($message);
	}
}
