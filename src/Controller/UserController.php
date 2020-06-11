<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     *
     * @Route("/user/setmail", name="user_setmail")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function user_setmail(Request $request)
    {
        if(!($this->isGranted("ROLE_USER")))
        {
            $this->addFlash("error", "Nie jesteś zalogoawny!");
            return $this->redirectToRoute("tasks_list");
        }
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, ['label' => 'Email: '])
            ->add('save', SubmitType::class, ['label' => 'Zapisz'])
            ->getForm();

        if ($request->isMethod("post"))
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash("success", "Email zmieniony");

                return $this->redirectToRoute("user_setmail");
            }

            $this->addFlash("error", "Email jest niepoprawny!");
        }

        return $this->render("user/set_email.html.twig", ["form" => $form->createView()]);
    }
}