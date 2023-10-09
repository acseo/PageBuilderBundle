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

use ACSEO\PageBuilderBundle\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

class PageSaver implements PageSaverInterface
{
    private $pageRepository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->pageRepository = $em->getRepository(Page::class);
    }

    public function save(array $data): Page
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
