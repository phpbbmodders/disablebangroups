<?php
/**
 *
 * Disable Ban Groups extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\disablebangroups\migrations\v10x;

class m1_initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'groups', 'group_ban');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	/**
	 * Update database schema
	 */
	public function update_schema()
	{
		return [
			'add_columns'		=> [
				$this->table_prefix . 'groups'			=> [
					'group_ban'					=> ['UINT', 0],
				],
			],
		];
	}

	/**
	 * Revert database schema
	 */
	public function revert_schema()
	{
		return [
			'drop_columns'		=> [
				$this->table_prefix . 'groups'			=> [
					'group_ban',
				],
			],
		];
	}
}
