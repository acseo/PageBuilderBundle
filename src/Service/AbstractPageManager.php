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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ACSEO\PageBuilderBundle\Entity\PageInterface;

abstract class AbstractPageManager
{
    public function load(Request $request): JsonResponse
    {
        $page = $this->loadPage($request);
        if ($page) {
            return new JsonResponse($page->getData());
        }

        return new JsonResponse();
    }

    abstract public function loadPage(Request $request) : PageInterface;
    abstract public function save(array $data): PageInterface;
}
