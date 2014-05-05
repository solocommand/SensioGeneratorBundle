<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Tests\Manipulator;

use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Tests\Mocks\AppKernel;

class KernelManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public static $kernel;
    public static $reflected;
    public static $originalSource;

    public static function setUpBeforeClass()
    {
        self::$kernel = new AppKernel('dev', true);
        self::$reflected = new \ReflectionObject(self::$kernel);
        self::$originalSource = file(self::$reflected->getFilename());
    }

    public static function tearDownAfterClass()
    {
        file_put_contents(self::$reflected->getFilename(), self::$originalSource);
    }

    public function setUp()
    {
        $this->kernelManipulator = new KernelManipulator(self::$kernel);
    }

    public function tearDown()
    {
        self::tearDownAfterClass();
    }

    public function testAddBundle()
    {
        $bundle = 'Vendor\TestBundle\VendorTestBundle';

        $this->assertTrue(
            $this->kernelManipulator->addBundle($bundle)
        );

        $content = file_get_contents(self::$reflected->getFilename());
        $this->assertContains("            new {$bundle}(),\n", $content);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testAddDuplicateBundle()
    {
        $bundle = 'Vendor\TestBundle\VendorTestBundle';

        $this->kernelManipulator->addBundle($bundle);
        $this->kernelManipulator->addBundle($bundle);
    }

    public function testAddBundles()
    {
        $bundles = array(
            'Sensio\TestBundle\SensioTestBundle',
            'Symfony\TestBundle\SymfonyTestBundle',
            'Doctrine\TestBundle\DoctrineTestBundle',
        );

        $this->assertTrue(
            $this->kernelManipulator->addBundles($bundles)
        );

        $content = file_get_contents(self::$reflected->getFilename());

        foreach ($bundles as $bundle) {
            $this->assertContains("            new {$bundle}(),\n", $content);
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testAddDuplicateBundles()
    {
        $bundles = array(
            'Sensio\TestBundle\SensioTestBundle',
            'Symfony\TestBundle\SymfonyTestBundle',
            'Doctrine\TestBundle\DoctrineTestBundle',
        );

        $this->kernelManipulator->addBundles($bundles);
        $this->kernelManipulator->addBundles($bundles);
    }
}
