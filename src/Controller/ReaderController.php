<?php

namespace App\Controller;

use App\Entity\Reader;
use App\Form\ReaderType;
use App\Repository\ReaderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/reader')]
class ReaderController extends AbstractController
{
    #[Route('/reader', name: 'app_reader')]
    public function index(): Response
    {
        return $this->render('reader/index.html.twig', [
            'controller_name' => 'ReaderController',
        ]);
    }




    #[Route('/add',name:'addReader')]
    public function add(ManagerRegistry $man ,Request $req,ReaderRepository $ra)
    {
       
        $r = new Reader();
        $form = $this->createForm(ReaderType::class, $r);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $em = $man->getManager();
            $em->persist($r);
            $em->flush();
                   }

                  
        return  $this->renderForm('reader/add.html.twig',
            ['f' => $form]
        );

    }
    
    #[Route('/edit/{id}', name: 'edit')]
    public function edit($id, ManagerRegistry $mr, ReaderRepository $repo, Request $req)
    {
        $r = $repo->find($id);
        $form = $this->createForm(ReaderType::class, $r);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->flush();
        }

        return $this->render('reader/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }



    #[Route('/fetch',name:'list')]
    public function fetchAuthor(ReaderRepository $repo)
    {
        return $this->render("reader/list.html.twig", [
            
            'readers' => $repo->findAll()

        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, ManagerRegistry $mr, ReaderRepository $repo)
    {
        $r = $repo->find($id);
        if ($r != null) {
            $em = $mr->getManager();
            $em->remove($r);
            $em->flush();
        }


        return $this->redirectToRoute('list');
       
    }

}
