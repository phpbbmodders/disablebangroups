<?php
/**
 *
 * Disable Ban Groups extension for the phpBB Forum Software package
 *
 * @copyright (c) 2024, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\disablebangroups\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Disable Ban Groups event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface  $db
	 * @param \phpbb\language\language           $language
	 * @param \phpbb\request\request             $request
	 * @param \phpbb\template\template           $template
	 * @param \phpbb\user                        $user
	 * @param string                             $table_prefix
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $table_prefix)
	{
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->table_prefix = $table_prefix;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.acp_manage_group_request_data'	=> 'acp_manage_group_request_data',
			'core.acp_manage_group_initialise_data'	=> 'acp_manage_group_initialise_data',
			'core.acp_manage_group_display_form'	=> 'acp_manage_group_display_form',

			'core.acp_ban_before'	=> 'ban_before',
			'core.mcp_ban_before'	=> 'ban_before',

			'core.user_setup'	=> 'user_setup',
		];
	}

	public function acp_manage_group_request_data($event)
	{
		$event->update_subarray('submit_ary', 'ban', $this->request->variable('group_ban', 0));
	}

	public function acp_manage_group_initialise_data($event)
	{
		$event->update_subarray('test_variables', 'ban', 'int');
	}

	public function acp_manage_group_display_form($event)
	{
		$this->template->assign_vars([
			'GROUP_BAN'	=> (!empty($event['group_row']['group_ban'])) ? ' checked="checked"' : '',
		]);
	}

	public function ban_before($event)
	{
		$group_ban_list = $this->group_ban_list();
		$ban_list = (!is_array($event['ban'])) ? array_unique(explode("\n", $event['ban'])) : $event['ban'];

		if ($event['mode'] == 'user')
		{
			// Select the relevant user_ids
			$sql_usernames = [];

			foreach ($ban_list as $username)
			{
				$username = trim($username);

				if ($username != '')
				{
					$clean_name = utf8_clean_string($username);

					$sql_usernames[] = $clean_name;
				}
			}

			$sql = 'SELECT user_id
				FROM ' . $this->table_prefix . 'users
				WHERE ' . $this->db->sql_in_set('username_clean', $sql_usernames);
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$banlist_ary[] = (int) $row['user_id'];
			}
			$this->db->sql_freeresult($result);

			$result = array_intersect($banlist_ary, $group_ban_list);

			if ($result && ($this->user->data['user_type'] != USER_FOUNDER))
			{
				trigger_error($this->language->lang('CANNOT_BAN_GROUP'));
			}
		}
	}

	/**
	 * Load common language files
	 */
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'phpbbmodders/disablebangroups',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	private function group_ban_list()
	{
		$sql_ary = [
			'SELECT'	=> 'g.group_id, g.group_ban, ug.user_id, ug.group_id',

			'FROM'		=> [
				$this->table_prefix . 'groups'	=> 'g',
			],

			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [
						$this->table_prefix . 'user_group'	=> 'ug'
					],
					'ON'	=> 'g.group_id = ug.group_id'
				],
			],

			'WHERE'		=> 'g.group_ban = 0'
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$group_ban_list = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_ban_list[] = $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		return $group_ban_list;
	}
}
