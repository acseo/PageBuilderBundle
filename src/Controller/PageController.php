<?php

/*
 * This file is part of the ACSEO/PageBuilder package.
 *
 * (c) ACSEO <contact@acseo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACSEO\PageBuilderBundle\Controller;

use ACSEO\PageBuilderBundle\Service\PageLoader;
use ACSEO\PageBuilderBundle\Service\PageSaver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/acseo-pagebuilder')]
class PageController extends AbstractController
{
    public function __construct(private PageLoader $pageLoader, private PageSaver $pageSaver)
    {
    }

    #[Route('/load', name: 'acseo_page_builder_load', methods: ['GET'])]
    public function pageLoad(Request $request): JsonResponse
    {
        return $this->pageLoader->load($request);
    }

    #[Route('/save', name: 'acseo_page_builder_save', methods: ['POST'])]
    public function pageSave(Request $request): Response
    {
        $this->pageSaver->save($request->toArray());

        return new Response();
    }
}
