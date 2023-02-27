<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\TestFixturesProvider;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

class UserControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?Router $urlGenerator = null;
    private UserRepository $userRepo;

    /** @throws Exception */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->userRepo = $this->client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
    }

    /** @dataProvider usersRouteDataProvider */
    public function testPermissionsOnUserPages(string $route, array $params = []): void
    {
        // As a visitor, test redirect to login page for current route
        $expectedRedirect = $this->urlGenerator->generate("login", referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($route, $params));
        $this->assertResponseRedirects($expectedRedirect);

        // Log-in non-admin user and check for 403 response
        $regularUser = TestFixturesProvider::getFixturesEntities()["user_1"];
        $this->client->loginUser($regularUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($route, $params));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Log-in admin user and check for 200 response
        $adminUser = TestFixturesProvider::getFixturesEntities()["user_admin"];
        $this->client->loginUser($adminUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate($route, $params));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function usersRouteDataProvider(): array
    {
        return [
            ["user_list"],
            ["user_create"],
            ["user_edit", ["id" => 1]],
        ];
    }

    public function testCreateUser(): void
    {
        $adminUser = TestFixturesProvider::getFixturesEntities()["user_admin"];
        $this->client->loginUser($adminUser);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate("user_create"));

        // Test the create user form as admin
        $form = $crawler->selectButton("Ajouter")->form();
        $form["user[username]"] = "TestAddUsername";
        $form["user[password][first]"] = "TestAddUserPassword";
        $form["user[password][second]"] = "TestAddUserPassword";
        $form["user[email]"] = "TestAddUserMail@test.tst";
        $this->client->submit($form);

        // Check redirects on form submission and value of front-end flash message
        $this->assertResponseRedirects($this->urlGenerator->generate("user_list"));
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success',"L'utilisateur a bien été ajouté.");

        // Finally, check that the user has been created in the test database
        $createdUser = $this->userRepo->findBy(["username" => "TestAddUsername"]);
        $this->assertNotEmpty($createdUser);
        $this->assertEquals("TestAddUserMail@test.tst", $createdUser[0]->getEmail());
    }

    public function testEditUser(): void
    {
        /** @var User $user2 */
        $adminUser = TestFixturesProvider::getFixturesEntities()["user_admin"];
        $user2 = TestFixturesProvider::getFixturesEntities()["user_2"];

        $this->client->loginUser($adminUser);
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate(
            "user_edit",
            ["id" => $user2->getId()]
        ));

        $this->assertFalse(in_array("ROLE_ADMIN", $user2->getRoles()));

        // Test the edit user form as admin : editing user_2, adding admin role
        $form = $crawler->selectButton("Modifier")->form();
        $form["user[username]"] = $user2->getUsername();
        $form["user[password][first]"] = $user2->getPassword();
        $form["user[password][second]"] = $user2->getPassword();
        $form["user[email]"] = $user2->getEmail();
        $form["user[is_admin]"] = true;
        $this->client->submit($form);

        // Check redirects on form submission and value of front-end flash message
        $this->assertResponseRedirects($this->urlGenerator->generate("user_list"));
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert.alert-success',"L'utilisateur a bien été modifié");

        // Finally, check that the user has been updated in the test database
        $updatedUser = $this->userRepo->find($user2->getId());
        $this->assertTrue(in_array("ROLE_ADMIN", $updatedUser->getRoles()));
    }
}
