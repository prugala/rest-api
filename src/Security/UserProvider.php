<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository){}

    public function loadUserByUsername(string $username): User
    {
        $user = $this->userRepository->findOneBy([
            'email' => $username,
            //'active' => true TODO
        ]);

        if ($user === null) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): void
    {
        throw new MethodNotImplementedException('refreshUser');
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}
