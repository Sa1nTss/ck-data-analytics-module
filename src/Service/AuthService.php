<?php

namespace App\Service;

use App\Entity\Roles;
use App\Entity\User;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

trait AuthService
{
    public function getAuthValue($user, string $value, ManagerRegistry $managerRegistry): mixed
    {
        $roles = $user->getRoles();

        $out = $managerRegistry->getRepository(Roles::class)->findBy([
            'roles_alt' => $roles,
            'delete' => false,
        ]);

        $auth_value = [];
        foreach ($out as $val) {
            $auth_value += unserialize($val->getAuthList());
        }

        if (empty($auth_value[$value])) {
            $response = new Response('Доступ запрещен');
            $response->headers->set('HTTP/1.1 403', 'Forbidden');

            return $response;
        } else {
            return $auth_value;
        }
    }
}
