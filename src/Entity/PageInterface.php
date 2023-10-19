<?php

/*
 * This file is part of the ACSEO/PageBuilder package.
 *
 * (c) ACSEO <contact@acseo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACSEO\PageBuilderBundle\Entity;

use ACSEO\PageBuilderBundle\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

interface PageInterface
{
    /**
     * Validate data structure.
     *
     * @return void
     */
    public static function validateFromArray(array $data);
    
    /**
     * DTO Method to create Page instance from an array of data.
     *
     * @return PageInterface
     */
    public static function createFromArray(array $data) : PageInterface;
    
    /**
     * Update Page Instance from an array of data.
     *
     * @return Page
     */
    public function updateFromArray(array $data): PageInterface;

    public function getData(): ?array;
}