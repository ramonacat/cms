<?php

declare(strict_types=1);

namespace Ramona\CMS\Admin\Authentication;

use Ramona\CMS\Admin\Frontend\CSSModule;

final readonly class LoginView
{
    public function __construct(
        public CSSModule $cssModule
    ) {
    }
}
