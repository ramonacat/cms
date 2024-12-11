<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin;

final readonly class LoginView
{
    public function __construct(
        public CSSModule $cssModule
    ) {
    }
}
