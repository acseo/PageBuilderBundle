<?php

/*
 * This file is part of the ACSEO/PageBuilder package.
 *
 * (c) ACSEO <contact@acseo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACSEO\PageBuilderBundle\Twig;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('PageBuilder', template: '@PageBuilder/components/pagebuilder.html.twig')]
class PageBuilder
{
    private array $config;
    private string $idField;
    private string $id;

    public function __construct(array $grapesjs, array $plugins = [], array $blocks = [])
    {
        $this->config['grapesjs'] = $grapesjs;
        $this->config['blocks'] = $blocks;
        $this->config['plugins'] = $plugins;
    }

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('id');
        $resolver->setRequired('idField');
        $resolver->setAllowedTypes('idField', 'string');

        return $resolver->resolve($data);
    }

    public function mount(string $idField, string $id = null)
    {
        if (!$id) {
            $this->id = uniqid('acseo_pagebuilder');
        }
        $this->idField = $idField;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdField()
    {
        return $this->idField;
    }

    public function getCustomBlocks()
    {
        if (isset($this->config['blocks'])) {
            return $this->config['blocks'];
        }

        return [];
    }

    /**
     * Return an array of all plugin URLS.
     */
    public function getPluginsURLs(): array
    {
        $urls = [];
        if (isset($this->config['plugins'])) {
            foreach ($this->config['plugins'] as $plugin) {
                if (isset($plugin['url'])) {
                    $urls[] = $plugin['url'];
                }
            }
        }

        return $urls;
    }

    public function getPluginsNames()
    {
        $names = [];
        if (isset($this->config['plugins'])) {
            foreach ($this->config['plugins'] as $plugin) {
                if (isset($plugin['name'])) {
                    $names[] = $plugin['name'];
                }
            }
        }

        return $names;
    }

    public function getGrapeJSConfig()
    {
        return $this->config['grapesjs'];
    }
}
