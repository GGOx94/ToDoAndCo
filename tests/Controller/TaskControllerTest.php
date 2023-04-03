<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Tests\TestFixturesProvider;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?Router $urlGenerator = null;
    private TaskRepository $taskRepo;

    private User $user;

    /** @throws Exception */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->taskRepo = $this->client->getContainer()->get('doctrine')->getManager()->getRepository(Task::class);
        $this->user = TestFixturesProvider::getFixturesEntities()["user_1"];
    }

    public function testListTasks(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list_todo'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPermissionsOnTaskPages(): void
    {
        $userTaskId = $this->user->getTasks()[0]->getId();
        $expectedRedirect = $this->urlGenerator->generate("login", referenceType: UrlGeneratorInterface::ABSOLUTE_URL);

        // Test permissions on task_create page
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_create"));
        $this->assertResponseRedirects($expectedRedirect);

        // Test permissions on task_edit page
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_edit", ["id" => $userTaskId]));
        $this->assertResponseRedirects($expectedRedirect);

        // Test permissions on task_delete page
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_delete", ["id" => $userTaskId]));
        $this->assertResponseRedirects($expectedRedirect);
    }

    public function testAddTask(): void
    {
        // Log user in, then ensure that the create task page works by submitting a Task form
        $this->client->loginUser($this->user);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_create"));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // From that page, test the create task form
        $form = $crawler->selectButton("Ajouter")->form();
        $createData = ["Create: Test Task Title", "Create: Test Task Content"];
        $form["task[title]"] = $createData[0];
        $form["task[content]"] = $createData[1];
        $this->client->submit($form);

        // Check redirects on form submission and value of front-end flash message
        $this->assertResponseRedirects($this->urlGenerator->generate("task_list_todo"));
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','a bien été ajoutée.');

        // Finally, check that the task has been created in the test database
        $createdTask = $this->taskRepo->findBy(["title" => $createData[0]]);
        $this->assertCount(1, $createdTask);
        $this->assertSame($createData[1], $createdTask[0]->getContent());
        $taskUser = $createdTask[0]->getUser();
        $this->assertSame($this->user->getId(), $taskUser->getId());
    }

    public function testEditTask(): void
    {
        $userTaskId = $this->user->getTasks()[0]->getId();

        // Log user in, then ensure that the edit page request returns 200(OK)
        $this->client->loginUser($this->user);

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_edit", ["id" => $userTaskId]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // From that page, test the edit task form
        $form = $crawler->selectButton("Modifier")->form();
        $editValues = ["Edit : Test Task Title", "Edit : Test Task Content"];
        $form["task[title]"] = $editValues[0];
        $form["task[content]"] = $editValues[1];
        $this->client->submit($form);

        // Check redirects on form submission and value of front-end flash message
        $this->assertResponseRedirects($this->urlGenerator->generate("task_list_todo"));
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','a bien été modifiée.');

        // Finally, check that the task has been updated in the test database
        $updatedTask = $this->taskRepo->find($userTaskId);
        $this->assertSame($editValues[0], $updatedTask->getTitle());
        $this->assertSame($editValues[1], $updatedTask->getContent());
    }

    public function testDeleteTask(): void
    {
        // Log user in, then load the task list page
        $this->client->loginUser($this->user);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("task_list_todo"));

        // From that page, select the first task delete button, then send the form
        $form = $crawler->selectButton("Supprimer")->form();
        $this->client->submit($form);

        // Check for controller redirections and front-end flash message
        $this->assertResponseRedirects($this->urlGenerator->generate("task_list_todo"));
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success','a bien été supprimée.');

        // Finally, check if the task has been removed from the tests database
        $taskId = explode("/", $form->getUri());
        $taskId = $taskId[count($taskId) - 2];
        $this->assertNull($this->taskRepo->find($taskId));
    }
}
