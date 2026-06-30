<?php

declare(strict_types=1);

namespace App\Controller;

use MyDashboard\Shared\Security\JwtUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAppController extends AbstractController
{
    protected function getOwnerId(): string
    {
        /** @var JwtUser $user */
        $user = $this->getUser();
        return $user->getUserId();
    }
}
