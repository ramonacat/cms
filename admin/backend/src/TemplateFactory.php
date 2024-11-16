<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

final class TemplateFactory
{
    public function __construct(
        private string $basePath
    ) {
    }

    /**
     * @template TModel of object
     * @param TModel $model
     * @return Template<TModel>
     */
    public function create(string $name, object $model): Template
    {
        return new Template($this->basePath . '/' . $name, $model);
    }
}
