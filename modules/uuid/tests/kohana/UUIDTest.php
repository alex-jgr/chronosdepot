<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Unit tests for UUID
 *
 * @group uuid
 *
 * @see UUID
 * @package    Unittest
 * @author     Kohana Team
 * @author     shadowhand <woody.gilk@kohanaframework.org>
 * @copyright  (c) 2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_UUIDTest extends Kohana_Unittest_TestCase {

	/**
	 * Provides test data for test_valid()
	 *
	 * @return array
	 */
	public function provider_valid()
	{
		return array(
			array('00000000-0000-0000-0000-000000000000', TRUE),
			array('26c8a352-60a5-342f-ae9b-0b06cde477fb', TRUE),
			array('3afb8d83-6af4-48f3-91f2-febbdc46e7a2', TRUE),
			array('f74bd7e3-ec3c-5c8a-aaeb-1b3c0dc16ea3', TRUE),
			array('6ba7b810-9dad-11d1-80b4-00c04fd430c8', TRUE),
			array('6ba7b810-9dad-11d1-80b4-zzzzzzzzzzzz', FALSE),
			array('00000000-0000-0000-000000000000', FALSE),
			array('6ba7b810-9dad-11d1-80b4-00c04fd430x0', FALSE),
		);
	}

	/**
	 * Tests UUID::valid()
	 *
	 * @test
	 * @dataProvider provider_valid
	 * @covers UUID::valid
	 * @param  string   $uuid      UUID to test validity
	 * @param  boolean  $expected  expect UUID to be valid?
	 */
	public function test_valid($uuid, $expected)
	{
		$this->assertEquals($expected, UUID::valid($uuid));
	}

	/**
	 * Tests UUID::v4()
	 *
	 * @test
	 * @covers UUID::v4
	 */
	public function test_v4_random()
	{
		$this->assertNotEquals('6ba7b810-9dad-11d1-80b4-00c04fd430c8', UUID::v4());
	}

	/**
	 * Provides test data for test_v3_md5()
	 *
	 * @return array
	 */
	public function provider_v3_md5()
	{
		return array(
			array('kohana', 'ffa14b9e-3afc-3989-95b7-cd49a421ee8a'),
			array('shadowhand', '819df73f-f819-3f53-946b-fd2e1c9f25a2'),
			array('zombor', 'fbb96ab5-e716-3920-91da-620a977db2cc'),
			array('team', 'db7ec69b-eb29-37ef-a76d-2e2ef553e92e'),
		);
	}

	/**
	 * Tests UUID::v3()
	 *
	 * @test
	 * @dataProvider provider_v3_md5
	 * @covers UUID::v3
	 * @param  string  $value     value to generate UUID from
	 * @param  string  $expected  UUID
	 */
	public function test_v3_md5($value, $expected)
	{
		$this->assertEquals($expected, UUID::v3(UUID::NIL, $value));
	}

	/**
	 * Provides test data for test_v3_md5()
	 *
	 * @return array
	 */
	public function provider_v5_sha1()
	{
		return array(
			array('kohana', '476f3195-2016-5eb4-8422-1505cb2c6066'),
			array('shadowhand', '93617e5a-9632-5d84-9512-16f76aa39015'),
			array('zombor', '996be374-db7b-5976-b822-f6ba1fae7337'),
			array('team', 'd221f29a-4332-5f0d-b323-c5206a2e86ce'),
		);
	}

	/**
	 * Tests UUID::v5()
	 *
	 * @test
	 * @dataProvider provider_v5_sha1
	 * @covers UUID::v5
	 * @param  string  $value     value to generate UUID from
	 * @param  string  $expected  UUID
	 */
	public function test_v5_sha1($value, $expected)
	{
		$this->assertEquals($expected, UUID::v5(UUID::NIL, $value));
	}

} // End Test Kohana_UUID
