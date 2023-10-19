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

use Symfony\Component\HttpFoundation\Request;
use ACSEO\PageBuilderBundle\Entity\PageInterface;
use ACSEO\PageBuilderBundle\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

class PageManager extends AbstractPageManager
{
    private $pageRepository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->pageRepository = $em->getRepository(Page::class);
    }

    public function loadPage(Request $request) : PageInterface
    {
        $uri = $request->query->get('uri', false);
        return $this->pageRepository->findOneByUri($uri);
    }

    public function save(array $data): PageInterface
    {
        Page::validateFromArray($data);

        $page = $this->pageRepository->findOneByUri($data['uri']);

        if (!$page) {
            $page = Page::createFromArray($data);
        } else {
            $page->updateFromArray($data);
        }

        $this->em->persist($page);
        $this->em->flush();

        return $page;
    }
}
