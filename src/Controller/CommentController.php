<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     *
     * @Route("/comment/add/{task}/{user}", name="comment_add")
     * @param Request $request
     * @param Task $task
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add_comment(Request $request, Task $task, User $user, \Swift_Mailer $mailer)
    {
        if(!($this->isGranted("ROLE_ADMIN")) && $task->getAddedBy() !== $user)
        {
            $this->addFlash("error", "Nie masz uprawnień do dodawania komentarzy!");
            return $this->redirectToRoute("tasks_list");
        }

        $comment = new Comment();
        $comment->setTask($task);
        $comment->setUser($user);

        $form = $this->createFormBuilder($comment)
            ->add('description', TextareaType::class, ['label' => 'Treść komentarza: '])
            ->add('save', SubmitType::class, ['label' => 'Zapisz komentarz'])
            ->getForm();

        if ($request->isMethod("post"))
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                $task->addCommentsId($comment);

                $this->addFlash("success", "Komentarz dodany");

                $message = (new \Swift_Message('Nowy komentarz do twojego zadania!'))
                    ->setFrom(['kontakt@max-play.pl' => 'Informator Zadań'])
                    ->setTo($user->getEmail() == null ? [] : $user->getEmail() )
                    ->setBody(
                        $this->renderView(
                            'emails/add_comment.html.twig',
                            ['task' => $task, 'comment'=>$comment]
                        ),
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute("tasks_list");
            }

            $this->addFlash("error", "Nie udało się dodać komentarza!");
        }

        return $this->render("comment/add_comment.html.twig", ["task"=>$task, "form" => $form->createView(), "comment" => $comment]);
    }
}
