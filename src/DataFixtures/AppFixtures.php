<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        // Create an admin user
        $admin = new User();
        $admin
            ->setEmail("admin@demo.oc")
            ->setUsername("admin")
            ->setPassword($this->hasher->hashPassword($admin, "Secret123"))
            ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        // Create an anonymous user
        $anon = new User();
        $anon
            ->setEmail("anon@demo.oc")
            ->setUsername("Anonyme")
            ->setPassword($this->hasher->hashPassword($admin, "Secret123"));

        $manager->persist($anon);

        // Create regular users
        $users = [];
        for($i = 1; $i < 6; $i++)
        {
            $user = new User();
            $user
                ->setUsername("user".$i)
                ->setEmail("user".$i."@demo.oc")
                ->setPassword($this->hasher->hashPassword($user,"Secret123"));

            $users[] = $user;

            $manager->persist($user);
        }

        // Create tasks, from users or anonymous
        for($i = 1; $i < 50; $i++)
        {
            $isAnonymous = mt_rand(0, 100) < 30;
            $isDone = mt_rand(0,100) < 50;

            $task = (new Task())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setTitle("Titre démo ".$i)
                ->setContent("Contenu démo ".$i)
                ->toggle($isDone)
                ->setCreatedAt($this->getImmutableDateDaysAgo(mt_rand(1, 60)));

            $task->setUser($isAnonymous ? $anon : $users[array_rand($users)]);

            $manager->persist($task);
        }

        $manager->flush();
    }

    /** @throws Exception */
    private function getImmutableDateDaysAgo(int $days) : \DateTimeImmutable
    {
        $daysInterval = new \DateInterval("P${days}D");
        $strDate = ((new \DateTime())->sub($daysInterval))->format(DateTimeInterface::ATOM);
        return new \DateTimeImmutable($strDate);
    }
}
