<?php

declare(strict_types=1);

/*
 * This file is part of the ACSEO/PageBuilder package.
 *
 * (c) ACSEO <contact@acseo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACSEO\PageBuilderBundle;

use ACSEO\PageBuilderBundle\Controller\PageController;
use ACSEO\PageBuilderBundle\Repository\PageRepository;
use ACSEO\PageBuilderBundle\Service\PageLoader;
use ACSEO\PageBuilderBundle\Service\PageSaver;
use ACSEO\PageBuilderBundle\Twig\PageBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class PageBuilderBundle extends AbstractBundle
{
    protected string $extensionAlias = 'acseo_page_builder';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('grapesjs')
                    ->children()
                        ->arrayNode('js')
                            ->beforeNormalization()
                                ->ifString()
                                    ->then(function ($value) {
                                        return [$value];
                                    })
                                ->end()
                            ->validate()
                                ->always(function ($value) {
                                    if (!\is_array($value)) {
                                        throw new \InvalidArgumentException('The "js" value must be a string or an array.');
                                    }

                                    return $value;
                                })
                            ->end()
                            ->scalarPrototype()->end()
                            ->defaultValue(['https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.7/grapes.min.js'])
                        ->end()
                        ->arrayNode('css')
                            ->beforeNormalization()
                                ->ifString()
                                    ->then(function ($value) {
                                        return [$value];
                                    })
                                ->end()
                            ->validate()
                                ->always(function ($value) {
                                    if (!\is_array($value)) {
                                        throw new \InvalidArgumentException('The "css" value must be a string or an array.');
                                    }

                                    return $value;
                                })
                            ->end()
                            ->scalarPrototype()->end()
                            ->defaultValue(['https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.7/css/grapes.min.css'])
                        ->end()
                        ->scalarNode('urlLoad')->defaultValue('acseo_page_builder_load')->end()
                        ->scalarNode('urlStore')->defaultValue('acseo_page_builder_save')->end()
                        ->scalarNode('pageController')->defaultValue(PageController::class)->end()
                    ->end()
                ->end()
                ->arrayNode('plugins')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('url')->isRequired()->end()
                            // TODO
                            // ->arrayNode('options')
                            //     ->scalarPrototype()->end()
                            // ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('blocks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->isRequired()->end()
                            ->scalarNode('category')->isRequired()->end()
                            ->scalarNode('media')->isRequired()->end()
                            ->scalarNode('content')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!isset($config['grapesjs'])) {
            return;
        }

        $builder->register(PageBuilder::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addArgument($config['grapesjs'])
            ->addArgument($config['plugins'] ?? [])
            ->addArgument($config['blocks'] ?? []);

        $builder->register(PageRepository::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        $builder->register(PageLoader::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        $builder->register(PageSaver::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);

        if (PageController::class === $config['grapesjs']['pageController']) {
            $builder->register(PageController::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);
        }
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
