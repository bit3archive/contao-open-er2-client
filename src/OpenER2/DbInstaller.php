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
 *
 *
 * This file is copied from the Contao Open Source CMS,
 * Formerly known as TYPOlight Open Source CMS.
 * Copyright (C) 2005-2011 Leo Feyer
 */

namespace OpenER2;

/**
 * Class DbInstaller
 *
 * Provide methods to handle database installs/updates.
 * @copyright  Leo Feyer 2005-2011
 * @copyright  InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author     Leo Feyer <http://www.contao.org>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 */
class DbInstaller
{
	/**
	 * The client instance.
	 *
	 * @var Client
	 */
	protected $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Compile a command array for each necessary database modification
	 * @return array
	 */
	public function compileCommands($arrFiles, $strDbPrefix = 'tl_')
	{
		$drop = array();
		$create = array();
		$return = array();

		$sql_current = $this->getFromDb($strDbPrefix);
		$sql_target = $this->getFromFiles($arrFiles, $strDbPrefix);

		// Create tables
		foreach (array_diff(array_keys($sql_target), array_keys($sql_current)) as $table)
		{
			$return['CREATE'][] = "CREATE TABLE `" . $table . "` (\n  " . implode(",\n  ", $sql_target[$table]['TABLE_FIELDS']) . (count($sql_target[$table]['TABLE_CREATE_DEFINITIONS']) ? ',' . "\n  " . implode(",\n  ", $sql_target[$table]['TABLE_CREATE_DEFINITIONS']) : '') . "\n)" . $sql_target[$table]['TABLE_OPTIONS'] . ';';
			$create[] = $table;
		}

		// Add or change fields
		foreach ($sql_target as $k=>$v)
		{
			if (in_array($k, $create))
			{
				continue;
			}

			// Fields
			if (is_array($v['TABLE_FIELDS']))
			{
				foreach ($v['TABLE_FIELDS'] as $kk=>$vv)
				{
					if (!isset($sql_current[$k]['TABLE_FIELDS'][$kk]))
					{
						$return['ALTER_ADD'][] = 'ALTER TABLE `'.$k.'` ADD '.$vv.';';
					}
					elseif ($sql_current[$k]['TABLE_FIELDS'][$kk] != $vv)
					{
						var_dump($sql_current[$k]['TABLE_FIELDS'][$kk], $vv);
						$return['ALTER_CHANGE'][] = 'ALTER TABLE `'.$k.'` CHANGE `'.$kk.'` '.$vv.';';
					}
				}
			}

			// Create definitions
			if (is_array($v['TABLE_CREATE_DEFINITIONS']))
			{
				foreach ($v['TABLE_CREATE_DEFINITIONS'] as $kk=>$vv)
				{
					if (!isset($sql_current[$k]['TABLE_CREATE_DEFINITIONS'][$kk]))
					{
						$return['ALTER_ADD'][] = 'ALTER TABLE `'.$k.'` ADD '.$vv.';';
					}
					elseif ($sql_current[$k]['TABLE_CREATE_DEFINITIONS'][$kk] != str_replace('FULLTEXT ', '', $vv))
					{
						$return['ALTER_CHANGE'][] = 'ALTER TABLE `'.$k.'` DROP INDEX `'.$kk.'`, ADD '.$vv.';';
					}
				}
			}

			// Move auto_increment fields to the end of the array
			if (isset($return['ALTER_ADD']) && is_array($return['ALTER_ADD']))
			{
				foreach (preg_grep('/auto_increment/i', $return['ALTER_ADD']) as $kk=>$vv)
				{
					unset($return['ALTER_ADD'][$kk]);
					$return['ALTER_ADD'][$kk] = $vv;
				}
			}

			if (isset($return['ALTER_CHANGE']) && is_array($return['ALTER_CHANGE']))
			{
				foreach (preg_grep('/auto_increment/i', $return['ALTER_CHANGE']) as $kk=>$vv)
				{
					unset($return['ALTER_CHANGE'][$kk]);
					$return['ALTER_CHANGE'][$kk] = $vv;
				}
			}
		}

		// Drop tables
		foreach (array_diff(array_keys($sql_current), array_keys($sql_target)) as $table)
		{
			$return['DROP'][] = 'DROP TABLE `'.$table.'`;';
			$drop[] = $table;
		}

		// Drop fields
		foreach ($sql_current as $k=>$v)
		{
			if (!in_array($k, $drop))
			{
				// Create definitions
				if (is_array($v['TABLE_CREATE_DEFINITIONS']))
				{
					foreach ($v['TABLE_CREATE_DEFINITIONS'] as $kk=>$vv)
					{
						if (!isset($sql_target[$k]['TABLE_CREATE_DEFINITIONS'][$kk]))
						{
							$return['ALTER_DROP'][] = 'ALTER TABLE `'.$k.'` DROP INDEX `'.$kk.'`;';
						}
					}
				}

				// Fields
				if (is_array($v['TABLE_FIELDS']))
				{
					foreach ($v['TABLE_FIELDS'] as $kk=>$vv)
					{
						if (!isset($sql_target[$k]['TABLE_FIELDS'][$kk]))
						{
							$return['ALTER_DROP'][] = 'ALTER TABLE `'.$k.'` DROP `'.$kk.'`;';
						}
					}
				}
			}
		}

		return $return;
	}


	/**
	 * Compile a table array from all SQL files and return it
	 * @return array
	 */
	protected function getFromFiles($arrFiles, $strDbPrefix)
	{
		$return = array();

		// Get all SQL files
		foreach ($arrFiles as $strFile)
		{
			if (!file_exists($strFile))
			{
				throw new Exception('Database file ' . $strFile . ' does not exists!');
			}

			$data = file($strFile);

			foreach ($data as $k=>$v)
			{
				$key_name = array();
				$subpatterns = array();

				// Unset comments and empty lines
				if (preg_match('/^[#-]+/i', $v) || !strlen(trim($v)))
				{
					unset($data[$k]);
					continue;
				}

				// Store table names
				if (preg_match('/^CREATE TABLE `([^`]+)`/i', $v, $subpatterns))
				{
					$table = $subpatterns[1];

					// skip tables with wrong prefix
					if (!preg_match('#^' . preg_quote($strDbPrefix) . '#i', $table))
					{
						$table = '';
					}
				}

				// Get table options
				elseif (strlen($table) && preg_match('/^\)([^;]+);/i', $v, $subpatterns))
				{
					$return[$table]['TABLE_OPTIONS'] = $subpatterns[1];
					$table = '';
				}

				// Add fields
				elseif (strlen($table))
				{
					preg_match('/^[^`]*`([^`]+)`/i', trim($v), $key_name);

					$first = preg_replace('/\s[^\n\r]+/i', '', $key_name[0]);
					$key = $key_name[1];

					// Create definitions
					if (in_array($first, array('KEY', 'PRIMARY', 'PRIMARY KEY', 'FOREIGN', 'FOREIGN KEY', 'INDEX', 'UNIQUE', 'FULLTEXT', 'CHECK')))
					{
						if (in_array($first, array('PRIMARY', 'PRIMARY KEY')))
						{
							$key = 'PRIMARY';
						}

						$return[$table]['TABLE_CREATE_DEFINITIONS'][$key] = preg_replace('/,$/i', '', trim($v));
					}
					else
					{
						$return[$table]['TABLE_FIELDS'][$key] = preg_replace('/,$/i', '', trim($v));
					}
				}
			}
		}

		return $return;
	}


	/**
	 * Compile a table array from the database and return it
	 * @return array
	 */
	protected function getFromDB($strDbPrefix)
	{
		/**
		 * @var \PDOStatement
		 */
		$objTables = $this->client->getDatabase()->query('SHOW TABLES');
		$tables = preg_grep('/^' . preg_quote($strDbPrefix) . '/i', $objTables->fetchAll(\PDO::FETCH_COLUMN, 0));

		if (!count($tables))
		{
			return array();
		}

		$return = array();

		foreach ($tables as $table)
		{
			/**
			 * @var \PDOStatement
			 */
			$fields = $this->list_fields($table);

			foreach ($fields as $field)
			{
				$name = $field['name'];
				$field['name'] = '`'.$field['name'].'`';

				if ($field['type'] != 'index')
				{
					unset($field['index']);

					// Field type
					if (!empty($field['length']))
					{
						$field['type'] .= '(' . $field['length'] . (!empty($field['precision']) ? ',' . $field['precision'] : '') . ')';

						unset($field['length']);
						unset($field['precision']);
					}

					// Default values
					if (in_array(strtolower($field['type']), array('text', 'tinytext', 'mediumtext', 'longtext', 'blob', 'tinyblob', 'mediumblob', 'longblob')) || stristr($field['extra'], 'auto_increment'))
					{
						unset($field['default']);
					}
					elseif (is_null($field['default']) || strtolower($field['default']) == 'null')
					{
						$field['default'] = "default NULL";
					}
					else
					{
						$field['default'] = "default '" . $field['default'] . "'";
					}

					$return[$table]['TABLE_FIELDS'][$name] = trim(implode(' ', $field));
				}

				// Indices
				if (!empty($field['index']) && $field['index_fields'])
				{
					$index_fields = implode('`, `', $field['index_fields']);

					switch ($field['index'])
					{
						case 'UNIQUE':
							if ($name == 'PRIMARY')
							{
								$return[$table]['TABLE_CREATE_DEFINITIONS'][$name] = 'PRIMARY KEY  (`'.$index_fields.'`)';
							}
							else
							{
								$return[$table]['TABLE_CREATE_DEFINITIONS'][$name] = 'UNIQUE KEY `'.$name.'` (`'.$index_fields.'`)';
							}
							break;

						case 'FULLTEXT':
							$return[$table]['TABLE_CREATE_DEFINITIONS'][$name] = 'FULLTEXT KEY `'.$name.'` (`'.$index_fields.'`)';
							break;

						default:
							$return[$table]['TABLE_CREATE_DEFINITIONS'][$name] = 'KEY `'.$name.'` (`'.$index_fields.'`)';
							break;
					}

					unset($field['index_fields']);
					unset($field['index']);
				}
			}
		}

		return $return;
	}


	/**
	 * Copied from Contao DB_Mysql class.
	 *
	 * Return a standardized array with field information
	 *
	 * Standardized format:
	 * - name:       field name (e.g. my_field)
	 * - type:       field type (e.g. "int" or "number")
	 * - length:     field length (e.g. 20)
	 * - precision:  precision of a float number (e.g. 5)
	 * - null:       NULL or NOT NULL
	 * - default:    default value (e.g. "default_value")
	 * - attributes: attributes (e.g. "unsigned")
	 * - index:      PRIMARY, UNIQUE or INDEX
	 * - extra:      extra information (e.g. auto_increment)
	 * @param string
	 * @return string
	 * @todo Support all kind of keys (e.g. FULLTEXT or FOREIGN).
	 */
	protected function list_fields($strTable)
	{
		$arrReturn = array();
		$arrFields = $this->client->getDatabase()->query('SHOW COLUMNS FROM `' . $strTable . '`')->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($arrFields as $k=>$v)
		{
			$arrChunks = preg_split('/(\([^\)]+\))/', $v['Type'], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

			$arrReturn[$k]['name'] = $v['Field'];
			$arrReturn[$k]['type'] = $arrChunks[0];

			if (!empty($arrChunks[1]))
			{
				$arrChunks[1] = str_replace(array('(', ')'), array('', ''), $arrChunks[1]);
				$arrSubChunks = explode(',', $arrChunks[1]);

				$arrReturn[$k]['length'] = trim($arrSubChunks[0]);

				if (!empty($arrSubChunks[1]))
				{
					$arrReturn[$k]['precision'] = trim($arrSubChunks[1]);
				}
			}

			if (!empty($arrChunks[2]))
			{
				$arrReturn[$k]['attributes'] = trim($arrChunks[2]);
			}

			if (!empty($v['Key']))
			{
				switch ($v['Key'])
				{
					case 'PRI':
						$arrReturn[$k]['index'] = 'PRIMARY';
						break;

					case 'UNI':
						$arrReturn[$k]['index'] = 'UNIQUE';
						break;

					case 'MUL':
						// Ignore
						break;

					default:
						$arrReturn[$k]['index'] = 'KEY';
						break;
				}
			}

			$arrReturn[$k]['null'] = ($v['Null'] == 'YES') ? 'NULL' : 'NOT NULL';
			$arrReturn[$k]['default'] = $v['Default'];
			$arrReturn[$k]['extra'] = $v['Extra'];
		}

		$arrIndexes = $this->client->getDatabase()->query("SHOW INDEXES FROM `$strTable`")->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($arrIndexes as $arrIndex)
		{
			$arrReturn[$arrIndex['Key_name']]['name'] = $arrIndex['Key_name'];
			$arrReturn[$arrIndex['Key_name']]['type'] = 'index';
			$arrReturn[$arrIndex['Key_name']]['index_fields'][] = $arrIndex['Column_name'];
			$arrReturn[$arrIndex['Key_name']]['index'] = (($arrIndex['Non_unique'] == 0) ? 'UNIQUE' : 'KEY');
		}

		return $arrReturn;
	}
}

?>