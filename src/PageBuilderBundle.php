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
use ACSEO\PageBuilderBundle\Service\PageManager;
use ACSEO\PageBuilderBundle\Twig\PageBuilder;
use ACSEO\PageBuilderBundle\Twig\PageRender;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class PageBuilderBundle extends AbstractBundle
{
    protected string $extensionAlias = 'acseo_page_builder';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('grapesjs')
                    ->treatNullLike(array())
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
                    ->isRequired()
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
                            ->variableNode('content')->isRequired()->end()
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
            ->setAutoconfigured(true)
            ->addArgument($config['grapesjs'])
            ->addArgument($config['plugins'] ?? [])
            ->addArgument($config['blocks'] ?? []);

        $builder->register(PageRender::class)
            ->setArgument('$httpKernelRuntime', new Reference('twig.runtime.httpkernel'))
            ->setArgument('$router', new Reference('router'))
            ->setAutoconfigured(true)
            ;

        $builder->register(PageRepository::class)
            ->setArgument('$registry', new Reference('doctrine'))
            ->setAutoconfigured(true);

        if (!$builder->hasAlias('acseo.page_builder.page_manager'))
        {
            $builder->register(PageManager::class)
                ->setArgument('$em', new Reference('doctrine.orm.entity_manager'))
                ->setAutoconfigured(true);
            $builder->setAlias('acseo.page_builder.page_manager', PageManager::class);
        }

        if (PageController::class === $config['grapesjs']['pageController']) {
            $builder->register(PageController::class)
                ->setAutowired(true)
                ->setArgument('$pageManager', new Reference('acseo.page_builder.page_manager'))
                ->setAutoconfigured(true);
        }
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
