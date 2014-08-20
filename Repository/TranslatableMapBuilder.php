<?php

namespace FSi\Bundle\AdminTranslatableBundle\Repository;

use FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager;
use FSi\Bundle\ResourceRepositoryBundle\Exception\ConfigurationException;
use FSi\Bundle\ResourceRepositoryBundle\Repository\MapBuilder as BaseMapBuilder;
use FSi\Bundle\ResourceRepositoryBundle\Repository\Resource\Type\ResourceInterface;

class TranslatableMapBuilder extends BaseMapBuilder
{
    /**
     * @var \FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager
     */
    protected $localeManager;

    /**
     * @var array
     */
    protected $keyMap;

    /**
     * @param string $mapPath
     * @param string[] $resourceTypes
     * @param \FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager $localeManager
     */
    public function __construct($mapPath, $resourceTypes = array(), LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
        $this->keyMap = array();
        parent::__construct($mapPath, $resourceTypes);
    }

    /**
     * @param array $rawMap
     * @param null|string $parentPath
     * @throws \FSi\Bundle\ResourceRepositoryBundle\Exception\ConfigurationException
     * @return array
     */
    protected function recursiveParseRawMap($rawMap = array(), $parentPath = null)
    {
        $map = array();

        if (!is_array($rawMap)) {
            return $map;
        }

        foreach ($rawMap as $key => $configuration) {
            $path = (isset($parentPath))
                ? $parentPath . '.' . $key
                : $key;

            $this->validateConfiguration($configuration, $path);

            if ($configuration['type'] == 'group') {
                unset($configuration['type']);
                $map[$key] = $this->recursiveParseRawMap($configuration, $path);
                continue;
            }

            $this->addToKeyMap($configuration, $path);

            $path = $this->getTranslatedKey($path);

            $this->validateResourceConfiguration($configuration);

            $resource = $this->createResource($configuration, $path);
            $this->addConstraints($resource, $configuration);
            $this->setFormOptions($resource, $configuration);

            $map[$key] = $resource;
            $this->resources[$path] = $map[$key];
        }

        return $map;
    }

    /**
     * @param $configuration
     * @param $path
     * @throws \FSi\Bundle\ResourceRepositoryBundle\Exception\ConfigurationException
     */
    protected function validateConfiguration($configuration, $path)
    {
        if (strlen($path) > 255) {
            throw new ConfigurationException(
                sprintf('"%s..." key is too long. Maximum key length is 255 characters', substr($path, 0, 32))
            );
        }

        if (!array_key_exists('type', $configuration)) {
            throw new ConfigurationException(
                sprintf('Missing "type" declaration in "%s" element configuration', $path)
            );
        }
    }

    /**
     * @param $configuration
     * @throws \FSi\Bundle\ResourceRepositoryBundle\Exception\ConfigurationException
     */
    protected function validateResourceConfiguration($configuration)
    {
        $validKeys = array(
            'form_options',
            'constraints',
            'translatable'
        );

        foreach ($configuration as $key => $options) {
            if ($key === 'type') {
                continue;
            }

            if ($key === 'translatable' && !is_bool($options)) {
                throw new ConfigurationException('Invalid value of "translatable" option. This option accepts only boolean value.');
            }

            if (!in_array($key, $validKeys)) {
                throw new ConfigurationException(
                    sprintf('"%s" is not a valid resource type option. Try one from: %s', $key, implode(', ', $validKeys))
                );
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function translateKey($key)
    {
        return sprintf('%s.%s', $key, $this->localeManager->getLocale());
    }

    /**
     * @param array $configuration
     * @param string $key
     */
    private function addToKeyMap(array $configuration, $key)
    {
        if(isset($configuration['translatable']) && $configuration['translatable'] === true) {
            $this->keyMap[$key] = $this->translateKey($key);

            return;
        }

        $this->keyMap[$key] = $key;
    }

    /**
     * Get resource definition by key.
     * It can return resource definition object or array if key represents resources group
     *
     * @param $key
     * @return mixed
     */
    public function getResource($key)
    {
        return $this->resources[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getTranslatedKey($key)
    {
        return $this->keyMap[$key];
    }
}