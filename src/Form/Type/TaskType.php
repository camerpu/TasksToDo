<?php

namespace App\Form\Type;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, ['label' => "Tytuł", 'required' => true])
            ->add("description", TextareaType::class, ["label" => "Opis", 'required' => true,])
            ->add("priority", ChoiceType::class, [
                'choices'  => [
                Task::PR_LOW => Task::PR_LOW,
                Task::PR_MEDIUM => Task::PR_MEDIUM,
                Task::PR_HIGH => Task::PR_HIGH
            ], "label" => "Priorytet", 'required' => true,])
            ->add("deadline", DateTimeType::class, [
                'label' => "Deadline",
                'required' => true,
            ])
            ->add("submit", SubmitType::class, ["label" => "Zapisz"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Task::class]);
    }
}
