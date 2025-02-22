<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Blocks\Services;

use Doctrine\ORM\EntityManagerInterface;
use Ramona\CMS\Admin\Blocks\Block;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

final class Blocks
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return list<Block>
     */
    public function all(): array
    {
        return $this
            ->entityManager
            ->getRepository(Block::class)
            ->findAll();
    }

    public function create(string $content): void
    {
        $block = new Block(Uuid::uuid7(), $content);

        $this->entityManager->persist($block);
        $this->entityManager->flush();
    }

    public function update(UuidInterface $id, string $newContent): void
    {
        $this->entityManager->wrapInTransaction(function () use ($id, $newContent) {
            $block = $this->find($id);

            $block->replaceContent($newContent);

            $this->entityManager->persist($block);
            $this->entityManager->flush();
        });
    }

    public function find(UuidInterface $id): Block
    {
        $block = $this->entityManager->find(Block::class, $id);

        if ($block === null) {
            throw new RuntimeException("Block with ID '{$id}' does not exist.");
        }

        return $block;
    }

    public function delete(UuidInterface $id): void
    {
        $this->entityManager->wrapInTransaction(function () use ($id) {
            $block = $this->find($id);

            $this->entityManager->remove($block);
            $this->entityManager->flush();
        });
    }
}
