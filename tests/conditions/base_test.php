<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\tests\conditions;

/**
 * Run tests on the type base class.
 * Because it is abstracted we will base these tests from the posts class.
 */
class base_test extends base
{
	protected $condition_type = 'phpbb.autogroups.type.posts';

	public function setUp()
	{
		parent::setUp();

		$this->condition = new \phpbb\autogroups\conditions\type\posts($this->db, $this->user, 'phpbb_autogroups_rules', 'phpbb_autogroups_types', $this->root_path, $this->php_ext);
	}

	/**
	 * Data for test_get_group_rules
	 */
	public function get_group_rules_test_data()
	{
		return array(
			array(
				'phpbb.autogroups.type.posts',
				array(
					array(
						'autogroups_id' 		=> 1,
						'autogroups_type_id'	=> 1,
						'autogroups_min_value'	=> 10,
						'autogroups_max_value'	=> 20,
						'autogroups_group_id'	=> 2,
						'autogroups_default'	=> 1,
					),
					array(
						'autogroups_id' 		=> 2,
						'autogroups_type_id'	=> 1,
						'autogroups_min_value'	=> 100,
						'autogroups_max_value'	=> 200,
						'autogroups_group_id'	=> 3,
						'autogroups_default'	=> 1,
					),
					array(
						'autogroups_id' 		=> 3,
						'autogroups_type_id'	=> 1,
						'autogroups_min_value'	=> 500,
						'autogroups_max_value'	=> 0,
						'autogroups_group_id'	=> 4,
						'autogroups_default'	=> 0,
					),
				),
			),
		);
	}

	/**
	 * Test the get_group_rules method
	 *
	 * @dataProvider get_group_rules_test_data
	 */
	public function test_get_group_rules($type, $expected)
	{
		$result = $this->condition->get_group_rules($type);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Data for test_get_users_groups
	 */
	public function get_users_groups_test_data()
	{
		return array(
			array(1, array(1, 5)),
			array(2, array(1, 2)),
		);
	}

	/**
	 * Test the get_users_groups method
	 *
	 * @dataProvider get_users_groups_test_data
	 */
	public function test_get_users_groups($user_id, $expected)
	{
		// Set the user id
		$this->user->data['user_id'] = $user_id;

		// Get the user's groups
		$result = $this->condition->get_users_groups();

		// Assert the user's groups are as expected
		$this->assertEquals($expected, $result);
	}

	/**
	 * Data for test_add_user_to_groups
	 */
	public function add_user_to_groups_test_data()
	{
		return array(
			array(
				1, // add user 1 to group 2, default enabled
				array(
					2 => 1,
				),
				array(1, 5, 2)),
			array(
				1, // add user 1 to group 2, default disabled
				array(
					2 => 0,
				),
				array(1, 5, 2)),
			array(
				1, // add user 1 to no group
				array(),
				array(1, 5)),
			array(
				2, // add user 2 to multiple groups, no defaults
				array(
					3 => 0,
					4 => 0,
					5 => 0,
				),
				array(1, 2, 3, 4, 5)),
			array(
				2, // add user 2 to multiple groups, defaults
				array(
					3 => 1,
					4 => 1,
					5 => 1,
				),
				array(1, 2, 3, 4, 5)),
		);
	}

	/**
	 * Test the add_user_to_groups method
	 *
	 * @dataProvider add_user_to_groups_test_data
	 */
	public function test_add_user_to_groups($user_id, $groups_data, $expected)
	{
		// Set the user id
		$this->user->data['user_id'] = $user_id;

		// Add the user to groups
		$this->condition->add_user_to_groups($groups_data);

		// Get the user's groups
		$result = $this->condition->get_users_groups();

		// Assert the user's groups are as expected
		$this->assertEquals($expected, $result);
	}

	/**
	 * Data for test_remove_user_from_groups
	 */
	public function remove_user_from_groups_test_data()
	{
		return array(
			array(
				1, // remove user 1 from group 5
				array(5),
				array(1)),
			array(
				1, // remove user 1 from no group
				array(),
				array(1, 5)),
			array(
				1, // remove user 1 from all their groups
				array(1, 5),
				array()),
			array(
				2, // remove user 2 from all their groups
				array(1, 2),
				array()),
			array(
				2, // remove user 2 from a group they do not belong to
				array(5),
				array(1, 2)),
			array(
				2, // remove user 2 from a group they do and do not belong to
				array(2, 5),
				array(1)),
		);
	}

	/**
	 * Test the remove_user_from_groups method
	 *
	 * @dataProvider remove_user_from_groups_test_data
	 */
	public function test_remove_user_from_groups($user_id, $groups_data, $expected)
	{
		// Set the user id
		$this->user->data['user_id'] = $user_id;

		// Add the user to groups
		$this->condition->remove_user_from_groups($groups_data);

		// Get the user's groups
		$result = $this->condition->get_users_groups();

		// Assert the user's groups are as expected
		$this->assertEquals($expected, $result);
	}
}
