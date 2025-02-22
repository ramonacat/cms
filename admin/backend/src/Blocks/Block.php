<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table('blocks')]
final class Block
{
    #[ORM\Id]
    #[ORM\Column('id', 'uuid')]
    private readonly UuidInterface $id;

    #[ORM\Column('content', 'text')]
    private string $content;

    public function __construct(
        UuidInterface $id,
        string $content
    ) {
        $this->id = $id;
        $this->content = $content;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function replaceContent(string $newContent): void
    {
        $this->content = $newContent;
    }
}
