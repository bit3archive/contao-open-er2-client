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
 * Class VersionHelper
 *
 * @copyright InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author    Tristan Lins <tristan.lins@infinitysoft.de>
 */
class VersionHelper
{
	public static $mStatusName = array(
		'alpha1', 'alpha2', 'alpha3',
		'beta1', 'beta2', 'beta3',
		'rc1', 'rc2', 'rc3',
		'stable'
	);

	public static function calculateRecentMajorVersion($version)
	{
		return intval($version/10000000)*10000000+9999999;
	}

	public static function calculateRecentMinorVersion($version)
	{
		return intval($version/10000)*10000+9999;
	}

	public static function formatVersion($aVersion, $build = false)
	{
		$aVersion       = (int)$aVersion;
		if (!$aVersion) return '';
		$status         = $aVersion % 10;
		$aVersion       = (int)($aVersion / 10);
		$micro          = $aVersion % 1000;
		$aVersion       = (int)($aVersion / 1000);
		$minor          = $aVersion % 1000;
		$major          = (int)($aVersion / 1000);
		return "$major.$minor.$micro." . self::$mStatusName[$status] . ($build!==false ? '.' . $build : '');
	}

	public static function parseVersion($version)
	{
		if (preg_match('#^(?<major>\d+)\.(?<minor>\d+)\.(?<build>\d+)(?:(?:\.|\s+)(?<release>\w+))?$#', $version, $match))
		{
			return ($match['major'] * 10000000) + ($match['minor'] * 10000) + ($match['build'] * 10) + (isset($match['release']) ? array_search($match['release'], self::$mStatusName) : 9);
		}
		if (preg_match('#^(?<major>\d+)\.(?<minor>\d+)\.(?<release>[^\d]\w+)$#', $version, $match))
		{
			return ($match['major'] * 10000000) + ($match['minor'] * 10000) + array_search($match['release'], self::$mStatusName);
		}
		return -1;
	}
}
