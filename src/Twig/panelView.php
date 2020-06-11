<?php

namespace App\Twig;

use App\Entity\Task;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class panelView extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('panelStyle', [$this, 'panelStyle']),
            new TwigFunction('getCollection', [$this, 'getCollection']),
        ];
    }

    public function panelStyle(Task $task)
    {
        if($task->getPriority() == Task::PR_HIGH)
            return 'bg-danger text-white';
        else
            return '';
    }

    public function getCollection(Task $task)
    {
        return $task->getCommentsId();
    }
}