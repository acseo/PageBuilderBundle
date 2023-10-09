<?php

/*
 * This file is part of the ACSEO/PageBuilder package.
 *
 * (c) ACSEO <contact@acseo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACSEO\PageBuilderBundle\Service;

use ACSEO\PageBuilderBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PageLoader implements PageLoaderInterface
{
    public function __construct(private PageRepository $pageRepository)
    {
    }

    public function load(Request $request): JsonResponse
    {
        $uri = $request->query->get('uri', false);
        $page = $this->pageRepository->findOneByUri($uri);
        if ($page) {
            return new JsonResponse($page->getData());
        }

        return new JsonResponse();
    }
}
