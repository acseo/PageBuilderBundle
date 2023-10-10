<?php

declare(strict_types=1);

namespace ACSEO\PageBuilderBundle\Tests\Unit\Entity;

use ACSEO\PageBuilderBundle\Entity\Page;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Exception\ValidationFailedException;

class PageTest extends TestCase
{

    /**
     * @dataProvider validPageDataProvider
     */
    public function testCreatePageValid(array $data)
    {
        try {
            $page = Page::createFromArray($data);
            $this->assertInstanceOf(Page::class, $page);
        } catch (\Throwable $e)
        {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @dataProvider invalidPageDataProvider
     */
    public function testCreatePageInvalid(array $data)
    {
        $this->expectException(ValidationFailedException::class);
        $page = Page::createFromArray($data);
    }


    // Data Provider pour des données valides
    public function validPageDataProvider()
    {
        return [
            [['uri' => 'test', 'html' => '<body><h1>ACSEO</h1></body>' , 'css' => 'h1 { color: black; }', 'data'=> []]],
        ];
    }

    // Data Provider pour des données valides
    public function invalidPageDataProvider()
    {
        return [
            [['uri' => null, 'html' => '<body><h1>ACSEO</h1></body>' , 'css' => 'h1 { color: black; }', 'data'=> []]],
            [['uri' => 'test', 'html' => null , 'css' => 'h1 { color: black; }', 'data'=> []]],
            [['uri' => 'test', 'html' => '<body><h1>ACSEO</h1></body>' , 'css' => null, 'data'=> []]],
            [['uri' => 'test', 'html' => '<body><h1>ACSEO</h1></body>' , 'css' => 'h1 { color: black; }', 'data'=> null]],
            [['uri' => null, 'html' => null , 'css' => null, 'data'=> null]],
            [['uri' => '', 'html' => '' , 'css' => '', 'data'=> '']],
        ];
    }
}
    