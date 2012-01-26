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

namespace Contao\OpenER2\Client\Dependency;

/**
 * Class Dependency
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class Dependency
{
	/**
	 * The extension, this dependency belongs to.
	 *
	 * @var \Contao\OpenER2\Client\Extension
	 */
	protected $extension;

	/**
	 * The oldest version of this dependency.
	 *
	 * @var int
	 */
	protected $minVersion;

	/**
	 * The newest version of this dependency.
	 *
	 * @var int
	 */
	protected $maxVersion;

	/**
	 * All parent dependencies, that are require this one.
	 *
	 * @var array<Dependency>
	 */
	protected $parents;

	/**
	 * Dependencies of this dependency.
	 *
	 * @var array<Dependency>
	 */
	protected $dependencies = array();

	/**
	 * Create a new dependency.
	 *
	 * @param \Contao\OpenER2\Client\Extension $extension
	 * @param int $minVersion
	 * @param int $maxVersion
	 */
	public function __construct(\Contao\OpenER2\Client\Extension $extension, $minVersion = 0, $maxVersion = 0)
	{
		$this->extension = $extension;
		$this->minVersion = $minVersion;
		$this->maxVersion = $maxVersion;
	}

	/**
	 * @param \Contao\OpenER2\Client\Extension $extension
	 */
	public function setExtension(\Contao\OpenER2\Client\Extension $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * @return \Contao\OpenER2\Client\Extension
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @param int $minVersion
	 */
	public function setMinVersion($minVersion)
	{
		$this->minVersion = $minVersion;
	}

	/**
	 * @return int
	 */
	public function getMinVersion()
	{
		return $this->minVersion;
	}

	/**
	 * @param int $maxVersion
	 */
	public function setMaxVersion($maxVersion)
	{
		$this->maxVersion = $maxVersion;
	}

	/**
	 * @return int
	 */
	public function getMaxVersion()
	{
		return $this->maxVersion;
	}

	public function addParent(Dependency $parent)
	{
		$this->parents[] = $parent;
	}

	/**
	 * @param array $parents
	 */
	public function setParents($parents)
	{
		$this->parents = $parents;
	}

	/**
	 * @return array
	 */
	public function getParents()
	{
		return $this->parents;
	}


	/**
	 * Add a new dependency.
	 *
	 * @param Dependency $dependency
	 */
	public function addDependency(Dependency $dependency)
	{
		if (isset($this->dependencies[$dependency->getExtension()->getName()]))
		{
			$exception = \Contao\OpenER2\Client\Exception\DuplicateDependencyException($this,
				$this->dependencies[$dependency->getExtension()->getName()],
				$dependency);

			// log the duplicate dependency exception
			$this->client->getLogger()
				->addError($exception->getMessage());

			throw new $exception;
		}
		$this->dependencies[$dependency->getExtension()->getName()] = $dependency;
		$dependency->addParent($this);
	}

	/**
	 * Set the dependencies.
	 *
	 * @param array<Dependency> $dependencies
	 */
	public function setDependencies($dependencies)
	{
		$this->dependencies = $dependencies;
	}

	/**
	 * Get the dependencies.
	 *
	 * @return array<Dependency>
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * Check if the dependency chain contains a extension.
	 *
	 * @param \Contao\OpenER2\Client\Extension $extension
	 * @return \Contao\OpenER2\Client\Dependency\Dependency
	 */
	public function dependsOn(\Contao\OpenER2\Client\Extension $extension)
	{
		/** @var \Contao\OpenER2\Client\Dependency $dependency */

		// check if this direct dependencies contains the requested extension
		foreach ($this->dependencies as $dependency)
		{
			if ($dependency->getExtension()->getName() == $extension->getName())
			{
				return $dependency;
			}
		}

		// recursive check the dependencies
		foreach ($this->dependencies as $dependency)
		{
			if ($tmp = $dependency->dependsOn($extension))
			{
				return $tmp;
			}
		}

		// this dependency does not depends on the requested extension
		return false;
	}

	/**
	 * Create a list of dependencies.
	 *
	 * @param bool $includeRoot
	 */
	public function toList($includeRoot = true)
	{
		$list = array();

		if ($includeRoot) {
			$list[$this->extension->getName()] = $this;
		}

		$tmp = array($this);

		while (count($tmp)) {
			$parent = array_pop($tmp);

			/** @var \Contao\OpenER2\Client\Dependency\Dependency $dependency */
			foreach ($parent->dependencies as $dependency)
			{
				if (!isset($list[$dependency->getExtension()->getName()]))
				{
					$blnModified = true;
					$list[$dependency->getExtension()->getName()] = $dependency;
					$tmp[] = $dependency;
				}
			}
		}

		return $list;
	}
}
