<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('task/index.html.twig', [
            'form' => $form->createView(),
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/status/{id}", name="status")
     */
    public function status(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        if (!$task) {
            $this->addFlash('error', 'Task not found');
        } else {
            $task->setDone(!$task->getDone());
            $em->persist($task);
            $em->flush();
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $this->getDoctrine()->getRepository(Task::class)->find($id);

        if (!$task) {
            $this->addFlash('error', 'Task not found');
        } else {
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('index');
    }

}