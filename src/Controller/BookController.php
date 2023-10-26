<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\Book2Type;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/add',name:'addBook')]
    public function add(ManagerRegistry $man ,Request $req,AuthorRepository $ra)
    {
       
        $b = new Book();
        $b->setPublished(true);
        $form = $this->createForm(BookType::class, $b);
        
        
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em = $man->getManager();
            $a=$ra->find(($b->getAuthor())->getId());
            $nb=$a->getNbBooks() + 1;
            $a->setNbBooks($nb); 
            $em->persist($b);
            $em->persist($a);
            $em->flush();
            
                   }

                  
        return  $this->renderForm(
            'book/add.html.twig',
            ['f' => $form]
        );

    }
    
    #[Route('/edit/{id}', name: 'edit')]
    public function edit($id, ManagerRegistry $mr, BookRepository $repo, Request $req)
    {
        $book = $repo->find($id);
        $form = $this->createForm(Book2Type::class, $book);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->flush();
        }

        return $this->render('book/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }



    #[Route('/fetch',name:'list')]
    public function fetchAuthor(BookRepository $repo)
    {
        return $this->render("book/list.html.twig", [
            'booksp' => $repo->findBypublished(true),
            'booksnp' => $repo->findBypublished(false),
            'books' => $repo->findAll()

        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, ManagerRegistry $mr, BookRepository $repo,AuthorRepository $ra)
    {
        $book = $repo->find($id);
        if ($book != null) {
            $em = $mr->getManager();
            $a=$ra->find(($book->getAuthor())->getId());
            $nb=$a->getNbBooks() -1;
            $a->setNbBooks($nb);
            if($a->getNbBooks()==0)
            {$em->remove($a);}
            else 
            {$em->persist($a);}
            if($a->getNbBooks()==0)
            $em->remove($a);

            $em->remove($book);
            $em->flush();
        }


        return $this->redirectToRoute('list');
       
    }
    #[Route('/deleteNb0', name: 'delete0')]
    public function delete0( ManagerRegistry $mr, BookRepository $repo,AuthorRepository $repa)
    { 

        $authors= $repa->findBynb_books(0);
        $books = $repo->findByauthor($authors);
        if ($books != null) {
            $em = $mr->getManager();
            $em->remove($books);
            $em->flush();
        }


        return $this->render('book/list.html.twig', [
            "books" => $repo->findAll()
        ]);
    }
    #[Route('/show/{id}',name:'show')]
    public function showDetail($id,BookRepository $rb)
    {
        $book=$rb->find($id);
        return $this->render('book/show.html.twig',
       [
          "title"=>$book->getTitle(),
          "publicationDate"=>$book->getPublicationDate(),
          "author"=>$book->getAuthor()

       ] );


    }

}
