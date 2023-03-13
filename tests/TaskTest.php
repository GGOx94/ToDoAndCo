<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function testToggle()
    {
        $task = $this->getTaskEntity();
        $this->assertSame(false, $task->isDone());

        $task->toggle(true);
        $this->assertSame(true, $task->isDone());
    }

    public function testUserRelation()
    {
        $task = $this->getTaskEntity();
        $user = (new User())->setUsername("TestUser");

        $task->setUser($user);
        $this->assertSame($user, $task->getUser());
        $this->assertTrue($user->getTasks()->contains($task));
    }

    public function testTaskContent()
    {
        // Task content cannot be empty
        $task = $this->getTaskEntity()->setContent("");
        $this->assertHasErrors($task, 1);
    }

    public function testTaskTitle()
    {
        $task = $this->getTaskEntity();

        // Task title length must be within 3 to 80 characters
        $task->setTitle(bin2hex(random_bytes(45))); // 90 chars hexadecimal string
        $this->assertHasErrors($task, 1);

        $task->setTitle("aa");
        $this->assertHasErrors($task, 1);

        $task->setTitle("This is a good title");
        $this->assertHasErrors($task, 0);
    }

    public function assertHasErrors(Task $task, int $count)
    {
        self::bootKernel();
        $errors = self::getContainer()->get("validator")->validate($task);
        $this->assertCount($count, $errors);
    }

    private function getTaskEntity(): Task
    {
        return (new Task())
            ->setTitle("Test Title")
            ->setContent("Test Content")
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser(new User())
            ->setDone(false);
    }
}
