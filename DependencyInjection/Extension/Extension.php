<?php
/**
 * Symfony framework extensions.
 */
namespace Mekras\SymfonyExtensionsBundle\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;

/**
 * Service container extension.
 *
 * - Preconfigured file locator {@see getFileLocator()}.
 * - Preconfigured config loaders: {@see getXmlLoader()} and {@see getYamlLoader()}.
 * - Autoloading for common config files (see. {@see load()}).
 */
class Extension extends SymfonyExtension
{
    /**
     * Current container.
     *
     * @var ContainerBuilder
     */
    protected $container = null;

    /**
     * File locator.
     *
     * @var null|FileLocator
     */
    private $locator = null;

    /**
     * XML loader.
     *
     * @var null|XmlFileLoader
     */
    private $xmlLoader = null;

    /**
     * YAML loader.
     *
     * @var null|YamlFileLoader
     */
    private $yamlLoader = null;

    /**
     * Load bundle configuration.
     *
     * Automatically loads files (if exists):
     *
     * - config.(xml|yml)
     * - services.(xml|yml)
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     * @throws \LogicException
     *
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $extensions = ['xml', 'yml'];

        $names = $this->getConfigFiles();
        foreach ($names as $filename) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ('*' === $ext) {
                foreach ($extensions as $extension) {
                    $this->loadFile(substr($filename, 0, -1) . $extension);
                }
            } else {
                $this->loadFile($filename);
            }
        }
    }

    /**
     * Return list of file masks to load.
     *
     * Descendant code example:
     *
     * ```php
     * protected function getConfigFiles()
     * {
     *     return array_merge(parent::getConfigFiles(), ['foo.*', 'bar.yml']);
     * }
     * ```
     *
     * @return string[]
     */
    protected function getConfigFiles()
    {
        return ['config.*', 'services.*'];
    }

    /**
     * Return configuration value by key.
     *
     * @param array  $config  Configuration.
     * @param string $key     Key in "foo.bar.baz" format.
     * @param mixed  $default Default value if does not exist.
     *
     * @return mixed
     */
    protected function getConfigValue(array $config, $key, $default = null)
    {
        $parts = explode('.', $key);
        $value = $config;
        foreach ($parts as $part) {
            if (!array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }

    /**
     * Load configuration from file.
     *
     * @param string $filename File path relative to `Resources/config`.
     *
     * @throws \Exception
     * @throws \LogicException
     */
    protected function loadFile($filename)
    {
        try {
            $this->getFileLocator()->locate($filename);
        } catch (\InvalidArgumentException $e) {
            return;
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'xml':
                $this->getXmlLoader()->load($filename);
                break;
            case 'yml':
                $this->getYamlLoader()->load($filename);
                break;
        }
    }

    /**
     * Return XML loader.
     *
     * @return XmlFileLoader
     *
     * @throws \LogicException
     */
    protected function getXmlLoader()
    {
        if (null === $this->container) {
            throw new \LogicException(
                sprintf('$container property must be set before calling %s', __METHOD__)
            );
        }
        if (null === $this->xmlLoader) {
            $this->xmlLoader = new XmlFileLoader($this->container, $this->getFileLocator());
        }

        return $this->xmlLoader;
    }

    /**
     * Return YAML loader.
     *
     * @return YamlFileLoader
     *
     * @throws \LogicException
     */
    protected function getYamlLoader()
    {
        if (null === $this->container) {
            throw new \LogicException(
                sprintf('$container property must be set before calling %s', __METHOD__)
            );
        }
        if (null === $this->yamlLoader) {
            $this->yamlLoader = new YamlFileLoader($this->container, $this->getFileLocator());
        }

        return $this->yamlLoader;
    }

    /**
     * Return file locator.
     *
     * @return FileLocator
     */
    protected function getFileLocator()
    {
        if (null === $this->locator) {
            $this->locator = new FileLocator($this->getConfigPath());
        }

        return $this->locator;
    }

    /**
     * Return configuration folder path.
     *
     * @return string
     */
    protected function getConfigPath()
    {
        $class = new \ReflectionClass($this);

        return dirname(dirname($class->getFileName())) . '/Resources/config';
    }
}
