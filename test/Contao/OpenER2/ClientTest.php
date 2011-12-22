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

require_once '../../TestBase.php';

/**
 * Test class for Client.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Client;

	    // set logger
	    $this->object->getLogger()->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetStaticRepositoryUrl().
     */
    public function testSetStaticRepositoryUrl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetStaticRepositoryUrl().
     */
    public function testGetStaticRepositoryUrl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetWsdl().
     */
    public function testSetWsdl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetWsdl().
     */
    public function testGetWsdl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabaseDsn().
     */
    public function testSetDatabaseDsn()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabaseDsn().
     */
    public function testGetDatabaseDsn()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabaseUser().
     */
    public function testSetDatabaseUser()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabaseUser().
     */
    public function testGetDatabaseUser()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabasePassword().
     */
    public function testSetDatabasePassword()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabasePassword().
     */
    public function testGetDatabasePassword()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabasePersistent().
     */
    public function testSetDatabasePersistent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabasePersistent().
     */
    public function testGetDatabasePersistent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabaseCharset().
     */
    public function testSetDatabaseCharset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabaseCharset().
     */
    public function testGetDatabaseCharset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetDatabaseAutoUpdate().
     */
    public function testSetDatabaseAutoUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabaseAutoUpdate().
     */
    public function testGetDatabaseAutoUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetInstallationRootPath().
     */
    public function testSetInstallationRootPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetInstallationRootPath().
     */
    public function testGetInstallationRootPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetContaoER2Compat().
     */
    public function testSetContaoER2Compat()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testIsContaoER2Compat().
     */
    public function testIsContaoER2Compat()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetTlFilesPath().
     */
    public function testSetTlFilesPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetTlFilesPath().
     */
    public function testGetTlFilesPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Client::setContaoVersion
     * @covers Client::getContaoVersion
     * @todo Implement testSetContaoVersion().
     */
    public function testSetGetContaoVersion()
    {

	    // 2.9.1 stable  -> 20090019
	    $this->object->setContaoVersion('2.9.1 stable');
	    $this->assertEquals(20090019, $this->object->getContaoVersion());
	    // 2.10.0 beta1  -> 20100003
	    $this->object->setContaoVersion('2.10.0 beta1');
	    $this->assertEquals(20100003, $this->object->getContaoVersion());
	    // 2.10.0 rc1    -> 20100006
	    $this->object->setContaoVersion('2.10.0 rc1');
	    $this->assertEquals(20100006, $this->object->getContaoVersion());
	    // 2.10.3 stable -> 20100039
	    $this->object->setContaoVersion('2.10.3');
	    $this->assertEquals(20100039, $this->object->getContaoVersion());
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testSetLanguages().
     */
    public function testSetLanguages()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetLanguages().
     */
    public function testGetLanguages()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetLogger().
     */
    public function testGetLogger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetSoap().
     */
    public function testGetSoap()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetDatabase().
     */
    public function testGetDatabase()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testUpdateDatabaseSchema().
     */
    public function testUpdateDatabaseSchema()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetRepository().
     */
    public function testGetRepository()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers {className}::{origMethodName}
     * @todo Implement testGetLicenseRegistry().
     */
    public function testGetLicenseRegistry()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
