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

use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;

#[AsTwigComponent('PageRender', template: '@PageBuilder/components/pagerender.html.twig')]
class PageRender
{
    public string $html = '';

    public function __construct(
        private HttpKernelRuntime $httpKernelRuntime,
        private RouterInterface $router        
        )
    {
    }

    public function mount(string $html)
    {
        $this->html = $this->manageBlocksWithRenderAttribute($html);
    }

    /**
     * This method will replace all dom blocks with attribute  render="route_name"
     * By rendering the fragment and replacing it in the generated html
     *
     * @param [type] $html
     * @return string $html with rendered blocks
     */
    private function manageBlocksWithRenderAttribute($html) : string
    {
        $crawler = new Crawler($html);
        $filteredDivs = $crawler->filter('div[render]');
        
        foreach ($filteredDivs as $node) {
            $htmlSource = $node->ownerDocument->saveHTML($node);
            $arguments = [];
            $routeName = false;
            foreach ($node->attributes as $attribute) {
                if ($attribute->name == 'render') {
                    $routeName = $attribute->value;
                } else {
                    $arguments[$attribute->name] = $attribute->value;
                }
            }

            $url = $this->router->generate($routeName, $arguments);
            $htmlDestination = $this->httpKernelRuntime->renderFragment($url);

            $html = str_replace($htmlSource, $htmlDestination, $html);
        }

        return $html;
    }
}
