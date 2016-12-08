<?php
/**
 * Symfony framework extensions.
 */
namespace Mekras\SymfonyExtensionsBundle\Tests\DependencyInjection\Extension;

use Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension\Extension;
use Mekras\SymfonyExtensionsBundle\DependencyInjection\SymfonyExtensionsExtension;
use Mekras\SymfonyExtensionsBundle\SymfonyExtensionsBundle;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension\Extension.
 *
 * @covers \Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension\Extension
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getConfigValue().
     */
    public function testGetConfigValue()
    {
        $extension = new SymfonyExtensionsExtension();
        $getConfigValue = new \ReflectionMethod(Extension::class, 'getConfigValue');
        $getConfigValue->setAccessible(true);

        $config = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];

        static::assertEquals(null, $getConfigValue->invoke($extension, $config, 'bar'));
        static::assertEquals('baz', $getConfigValue->invoke($extension, $config, 'foo.bar'));
    }

    /**
     * Test configuration loading.
     */
    public function testLoad()
    {
        $locator = $this->getMock(FileLocatorInterface::class, ['locate']);
        $locator->expects(static::any())->method('locate')->willReturnCallback(
            function ($filename) {
                \PHPUnit_Framework_Assert::assertContains(
                    $filename,
                    ['config.xml', 'config.yml', 'services.xml', 'services.yml']
                );

                return __DIR__ . '/fixtures/' . $filename;
            }
        );

        $extension = $this->getMock(SymfonyExtensionsExtension::class, ['getFileLocator']);
        $extension->expects(static::any())->method('getFileLocator')->willReturn($locator);
        /** @var SymfonyExtensionsExtension $extension */
        $container = new ContainerBuilder();
        $extension->load([], $container);
    }
}
