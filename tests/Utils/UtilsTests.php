<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once dirname(__FILE__) . '/../../src/common/include/utils.php';
require_once dirname(__FILE__) . '/../../src/common/include/escapingUtils.php';

/**
 * Simple math test class.
 *
 * @package   Example
 * @author    Manuel Pichler <mapi@phpundercontrol.org>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 0.4.7
 * @link      http://www.phpundercontrol.org/
 */
class Utils_Tests extends PHPUnit_Framework_TestCase
{
	/**
	 * test the validate_email function.
	 */
	public function testEmail()
	{
		$this->assertTrue(validate_email('al@fx.fr'), 'al@fx.fr is a valid email address');

		$this->assertFalse(validate_email('al @fx.fr'), 'al @fx.fr is not a valid email address');

		$this->assertFalse(validate_email('al'), 'al is not a valid email address');
	}

	/**
	 * test the validate_hostname function.
	 */
	public function testHostname()
	{
		$this->assertTrue(valid_hostname('myhost.com'), 'myhost.com is a valid hostname.');

		$this->assertTrue(valid_hostname('myhost.com.'), 'myhost.com. is a valid hostname.');

		$this->assertFalse(valid_hostname('my host.com'), 'my host.com is not a valid hostname');

		$this->assertFalse(valid_hostname('O@O'), 'O@O is not a valid hostname');
	}

	/**
	 * test the util_check_url function.
	 */
	public function testUtilCheckUrl()
	{
		$this->assertTrue(util_check_url('http://fusionforge.org/'), 'http://fusionforge.org/ is a valid URL.');

		$this->assertTrue(util_check_url('https://fusionforge.org/'), 'https://fusionforge.org/ is a valid URL.');

		$this->assertTrue(util_check_url('ftp://fusionforge.org/'), 'ftp://fusionforge.org/ is a valid URL.');

		$this->assertFalse(util_check_url('webdav://toto'), 'webdav://toto is not a valid URL.');

		$this->assertFalse(util_check_url('fusionforge.org'), 'fusionforge.org is not a valid URL');
	}

	/**
	 * test the util_strip_accents() function.
	 */
	public function testStripAccents()
	{
		$this->assertEquals(util_strip_accents('aléiât'), 'aleiat');

		$this->assertEquals(util_strip_accents('ààéééïï'), 'aaeeeii');

		$this->assertEquals(util_strip_accents('alain'), 'alain');
	}
	
	public function testGetFilteredStringFromRequest()
	{
		$_REQUEST=array('arg' => 'good');
		$this->assertEquals(getFilteredStringFromRequest('arg', '/^[a-z]+$/', 'default'), 'good');

		$_REQUEST=array('arg' => 'BaD');
		$this->assertEquals(getFilteredStringFromRequest('arg', '/^[a-z]+$/', 'default'), 'default');

		$_REQUEST=array('no_arg' => 'BaD');
		$this->assertEquals(getFilteredStringFromRequest('arg', '/^[a-z]+$/', 'default'), 'default');
	}
}
