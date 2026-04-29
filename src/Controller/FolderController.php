<?php

namespace App\Controller;

use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FolderController extends AbstractController
{
    #[Route('/folder', name: 'app_folder')]
    public function index(): Response
    {
        return $this->render('folder/index.html.twig', [
            'controller_name' => 'FolderController',
        ]);
    }

    #[Route('/folder/new', name: 'app_folder_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
        $name = $request->request->get('folder_name');
        if ($name) {
            $folder = new Folder();
            $folder->setName($name);
            $folder->setU<wser($this->getUser());
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }
    }   
        return $this->render('folder/new.html.twig');
    }
}