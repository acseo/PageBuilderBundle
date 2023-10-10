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

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Unique]
    private ?string $uri = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $css = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = null;

    public static function propertyExists(string $value): bool
    {
        return property_exists(self::class, $value);
    }

    /**
     * Validate data structure.
     *
     * @return void
     */
    public static function validateFromArray(array $data)
    {
        $validator = Validation::createValidator();
        $constraint = new Assert\Collection([
            'uri' => new Assert\NotBlank(),
            'html' => new Assert\NotBlank(),
            'css' => new Assert\NotBlank(),
            'data' => new Assert\NotNull()
        ]);

        $violations = $validator->validate($data, $constraint);
        if (\count($violations) > 0) {
            throw new ValidationFailedException((object) $data, $violations);
        }
    }

    /**
     * DTO Method to create Page instance from an array of data.
     *
     * @return Page
     */
    public static function createFromArray(array $data)
    {
        self::validateFromArray($data);
        $page = new self();

        $page->uri = $data['uri'];
        $page->html = $data['html'];
        $page->css = $data['css'];
        $page->data = $data['data'];

        return $page;
    }

    /**
     * Update Page Instance from an array of data.
     *
     * @return Page
     */
    public function updateFromArray(array $data)
    {
        self::validateFromArray($data);
        $this->uri = $data['uri'];
        $this->html = $data['html'];
        $this->css = $data['css'];
        $this->data = $data['data'];

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
