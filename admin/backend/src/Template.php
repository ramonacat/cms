<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

use function Safe\ob_start;

/**
 * @template TModel of object
 */
final class Template
{
    /**
     * @param TModel $model
     */
    public function __construct(
        private string $path,
        private object $model,
    ) {
    }

    public function render(): string
    {
        ob_start();

        $model = $this->model;
        require $this->path;

        $result = ob_get_clean();
        assert($result !== false);

        return $result;
    }
}
