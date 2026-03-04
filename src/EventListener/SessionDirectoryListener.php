<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class SessionDirectoryListener
{
    public function __construct(private string $projectDir) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $path = $this->projectDir . '/var/sessions';

        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
    }
}
