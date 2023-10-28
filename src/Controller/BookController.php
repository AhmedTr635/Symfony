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
    //---------------------------------------------------------
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
//------------------------------------------------------------------------
    #[Route('/searchBookByref',name:'searchBookByref')]
    public function showBookRef(BookRepository $repo,Request $request)
    {

        $result=$repo->findAll();
        if($request->isMethod('post')){
            $value=$request->get('searchby');
            $result=$repo->searchBookByRef($value);
        return $this->renderForm('book/listSearchRef.html.twig', [
            'books'=>$result,
            ]);
        }

   return $this->renderForm('book/listSearchRef.html.twig', [
    'books'=>$result,
   ]);
    }

    //-----------------------------------------------------
    #[Route('/booksListByAuthorsSorted',name:'booksListByAuthors')]
    public function booksList(BookRepository $repo)
    {

        
        
            $books=$repo->booksListByAuthors();
        return $this->renderForm('book/listQueryBuilder.html.twig', [
            'books'=>$books
            ]);
        

   
    }

    //---------------------------------------
    #[Route('/booksListInf2023',name:'booksListInf2023')]
    public function booksListAvant2023(BookRepository $repo)
    {

        return $this->render('book/listQueryBuilder.html.twig', [
            'books'=>$repo->listBooksAvant2023()
            ]);
        

   
    }
//----------------------------------------------------------
#[Route('/updateSf_Romance')]
    public function updateSCf_Romance(BookRepository $repo)
    {
        $repo->updateSfToRomanceWithQueryBuilder();

        return $this->render('book/listQueryBuilder.html.twig', [
            'books'=>$repo->findAll()
            ]);
        

   
    }


    //---------------------------------------------
    #[Route('/listRomanceInf2018Sup2014')]
    public function listRomance(BookRepository $reb)
    {
        return $this->render('book/listQueryBuilder.html.twig',[

           'books'=>$reb->RomanceBooks() 
        ]);

    }
}
