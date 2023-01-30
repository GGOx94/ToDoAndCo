<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TaskRepository $repository
    ) {}

    #[Route("/tasks", name: "task_list")]
    public function listTasks() : Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->repository->findAll()]);
    }

    #[Route("/tasks/create", name: "task_create")]
    public function createTask(Request $request) : Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->toggle(false);
            $task->setUser($this->getUser());

            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'Votre tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/tasks/{id}/edit", name: "task_edit")]
    public function editTask(Task $task, Request $request) : Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route("/tasks/{id}/toggle", name: "task_toggle")]
    public function toggleTask(Task $task) : Response
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success',
            sprintf('La tâche %s a bien été marquée comme %s.',
            $task->getTitle(), $task->isDone() ? "faite" : "non terminée")
        );

        return $this->redirectToRoute('task_list');
    }

    #[Route("/tasks/{id}/delete", name: "task_delete")]
    public function deleteTask(Task $task) : Response
    {
        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
