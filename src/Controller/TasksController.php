<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Form\Type\TaskType;

class TasksController extends AbstractController
{
    /**
     * @Route("/", name="tasks_list")
     */
    public function index()
    {
        $eM = $this->getDoctrine()->getManager();
        $tasksActive = $eM->getRepository(Task::class)->findActive();
        $tasksEnded = $eM->getRepository(Task::class)->findEnded();
        return $this->render('tasks/index.html.twig', [
            'tasks_active' => $tasksActive,
            'tasks_ended' => $tasksEnded,
        ]);
    }

    /**
     * @Route("/task/{id}", name="show_task")
     *
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTask(Request $request, Task $task)
    {
        return $this->render('tasks/task.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/add", name="tasks_add")
     */
    public function add(Request $request, \Swift_Mailer $mailer)
    {
        if(!($this->isGranted("ROLE_USER")))
        {
            $this->addFlash("error", "Musisz się zalogować aby dodać zadanie!");
            return $this->redirectToRoute("tasks_list");
        }
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        if ($request->isMethod("post"))
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $task
                    ->setAddedBy($this->getUser())
                    ->setStatus(Task::ST_WAITING);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                $this->addFlash("success", "Zadanie {$task->getTitle()} zostało dodana.");

                //sending emails
                $eM = $this->getDoctrine()->getManager();
                $admins_db = $eM->getRepository(User::class)->findAdmins();
                $admins = [];
                foreach($admins_db as $admin)
                {
                    if($admin['email'] !== '')
                        $admins[] = $admin['email'];
                }
                $message = (new \Swift_Message('Pojawiło się nowe zadanie!'))
                    ->setFrom(['kontakt@max-play.pl' => 'Informator Zadań'])
                    ->setTo($admins)
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'emails/add_task.html.twig',
                            ['task' => $task]
                        ),
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute("tasks_list");
            }

            $this->addFlash("error", "Nie udało się dodać zadania!");
        }

        return $this->render("tasks/add.html.twig", ["form" => $form->createView()]);
    }


    /**
     * @Route("/tasks/take/{id}", name="take_task")
     *
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function takeTask(Request $request, Task $task)
    {
        if(!($this->isGranted("ROLE_ADMIN")))
        {
            $this->addFlash("error", "Nie masz uprawnień do podejmowania zadań!");
            return $this->redirectToRoute("tasks_list");
        }
        $task
            ->setStatus(Task::ST_TOOK)
            ->setTakenBy($this->getUser());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        $this->addFlash("success", "Zadanie zostało podjęte przez Ciebie");

        return $this->redirectToRoute("tasks_list");
    }

    /**
     * @Route("/tasks/delete/{id}", name="delete_task")
     *
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTask(Request $request, Task $task)
    {
        if(!($this->isGranted("ROLE_ADMIN")))
        {
            $this->addFlash("error", "Nie masz uprawnień do usuwania zadań!");
            return $this->redirectToRoute("tasks_list");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash("success", "Zadanie zostało pomyślnie usunięte");

        return $this->redirectToRoute("tasks_list");
    }

    /**
     * @Route("/tasks/end/{id}", name="end_task")
     *
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function endTask(Request $request, Task $task)
    {
        if(!($this->isGranted("ROLE_ADMIN")))
        {
            $this->addFlash("error", "Nie masz uprawnień do zamykania zadań!");
            return $this->redirectToRoute("tasks_list");
        }
        $task->setStatus(Task::ST_FINISHED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        $this->addFlash("success", "Zadanie zostało ukończone");

        return $this->redirectToRoute("tasks_list");
    }

    /**
     * @Route("/tasks/back/{id}", name="back_task")
     *
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function backTask(Request $request, Task $task)
    {
        if(!($this->isGranted("ROLE_ADMIN")))
        {
            $this->addFlash("error", "Nie masz uprawnień do przywracania zadań!");
            return $this->redirectToRoute("tasks_list");
        }
        if($task->getStatus() !== Task::ST_FINISHED)
        {
            $this->addFlash("error", "To zadanie nie jest jeszcze zakończone!");
            return $this->redirectToRoute("tasks_list");
        }
        $task->setStatus(Task::ST_TOOK);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        $this->addFlash("success", "Zadanie zostało przywrócone do aktywnych");

        return $this->redirectToRoute("tasks_list");
    }
}
