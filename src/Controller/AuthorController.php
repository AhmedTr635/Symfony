<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/Author')]
class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/add')]
    public function add(ManagerRegistry $man, Request $req)
    {
        $em = $man->getManager();
        $a = new Author();
        $form = $this->createForm(AuthorType::class, $a);
        $a->setNbBooks(0);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em = $man->getManager();
            $em->persist($a);
            $em->flush();
        }
        return  $this->renderForm(
            'author/add.html.twig',
            ['form' => $form]
        );
    }


    #[Route('/edit/{id}', name: 'editAuthor')]
    public function edit($id, ManagerRegistry $mr, AuthorRepository $repo, Request $req)
    {
        $author = $repo->find($id);
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->flush();
        }

        return $this->render('author/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/fetch')]
    public function fetchAuthor(AuthorRepository $repo)
    {
        
        return $this->render("author/list.html.twig", [
            'authors' => $repo->findAll()
        ]);
    }


    #[Route('/delete/{id}', name: 'deleteAuthor')]
    public function delete($id, ManagerRegistry $mr, AuthorRepository $repo)
    {
        $a = $repo->find($id);
        if ($a != null) {
            $em = $mr->getManager();
            $em->remove($a);
            $em->flush();
        }


        return $this->render('author/list.html.twig', [
            "authors" => $repo->findAll()
        ]);
    }

    #[Route('/listEmailQueryBuilder')]
    public function fetchQuereyBuilder(AuthorRepository $a)
    {
        $authors=$a->listAuthorByEmail();

        return $this->render('author/queryList.html.twig',
        [
            'authors'=>$authors
        ]);


    }

    #[Route('/searchMaxMin')]
    public function searchMaxMin(AuthorRepository $rep, Request $request)
    {
        $authors=$rep->findAll();
        if($request->isMethod('post')){
            $Max=$request->get('MaxSearch');
            $Min=$request->get('MinSearch');

            $authors=$rep->searchBookMaxMin($Max,$Min);

    }
    return $this->render('author/maxMinNbBooks.html.twig',
    [
        'authors'=>$authors
    ]);
}
#[Route('/deleteDQLnbbooks')]
public function DqlDeleteNb0(AuthorRepository $repa)
{
    $repa->DeleteAuthors0();
    $authors=$repa->findAll();
    return $this->render('author/queryList.html.twig',
        [
            'authors'=>$authors
        ]);
    
}





}