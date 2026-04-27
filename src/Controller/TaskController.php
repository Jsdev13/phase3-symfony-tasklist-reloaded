<?php

namespace App\Controller;

use App\Entity\Task;
use App\Enum\Statut; 
use App\Repository\TaskRepository;
use App\Repository\PriorityRepository;
use App\Repository\FolderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

    #[Route('/task')]
final class TaskController extends AbstractController
{

    #[Route(name: 'app_task_index', methods: ['GET'])]
public function index(Request $request, TaskRepository $taskRepository, FolderRepository $folderRepository, PriorityRepository $priorityRepository): Response
{
    $status = $request->query->get('status');
    $priority = $request->query->get('priority');

    $criteria = [];
    
    if ($status) {
        $criteria['status'] = $status;
    }

    if ($priority) {
        $criteria['priority'] = $priority;
    }

    $tasks = $criteria ? $taskRepository->findBy($criteria) : $taskRepository->findAll();

    return $this->render('home/index.html.twig', [
        'tasks' => $tasks,
        'folders' => $folderRepository->findAll(),
        'priorities' => $priorityRepository->findAll()

    ]);
}

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FolderRepository $folderRepository, PriorityRepository $priorityRepository): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('task_title');
            $priorityId = $request->request->get('task_priority');
            $folderId = $request->request->get('task_folder');

            if ($title) {
                $task = new Task();
                $task->setTitle($title);
                $task->setStatus(Statut::EN_COURS);
                $task->setIsPinned(false);
                $task->setUser($this->getUser());

            if (!empty($folderId)) {
                $folder = $folderRepository->find($folderId);
                $task->setFolder($folder);
                } else {
                $task->setFolder(null);
            }
            if (!empty($priorityId)) {
                $priority = $priorityRepository->find($priorityId);
                $task->setPriority($priority);
            }

                $entityManager->persist($task);
                $entityManager->flush();

                return $this->redirectToRoute('app_task_index');
            }
        }

        return $this->render('task/new.html.twig', [ 
            'folders' => $folderRepository->findAll(),
            'priorities' => $priorityRepository->findAll()
            

        ]);
    }

    #[Route('/{id}/toggle', name: 'app_task_toggle', methods: ['POST'])]
    public function toggle(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $newStatut = ($task->getStatus() === Statut::EN_COURS) ? Statut::TERMINE : Statut::EN_COURS;
        
        $task->setStatus($newStatut);
        $em->flush();

        return new JsonResponse([
            'newStatus' => $newStatut->value,
            'id' => $task->getId()
        ]);
    }
}
