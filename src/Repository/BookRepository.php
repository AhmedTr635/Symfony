<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function searchBookByRef($r)
    {
        return $this->createQueryBuilder('b')
            ->where('b.ref=:r')
            ->setParameter('r', $r)
            ->getQuery()
            ->getResult();
    }
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function listBooksAvant2023()
    {$date = new \DateTime('2023-01-01');
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->where('a.nb_books > 10')
            ->andWhere('b.publicationDate < :date2024')
            ->setParameter('date2024', $date)
            ->getQuery()
            ->getResult();
    }
    public function RomanceBooks()
    {
        $date1 = new \DateTime('2014-01-01'); 
        $date2 = new \DateTime('2018-12-31'); 
        $em=$this->getEntityManager();
        $query=$em->createQuery('SELECT b from App\Entity\Book b where b.category =:Romance AND :d1 < b.publicationDate AND b.publicationDate < :d2');
        $query->setParameter('Romance','Romance');
        $query->setParameter('d1', $date1);
        $query->setParameter('d2', $date2);
        return $query->getResult();

    }
    
   public function updateSf_Romance()
   {
    $em=$this->getEntityManager();
    $query=$em->createQuery('UPDATE App\Entity\Book b set s.category =: typeRomance where s.category=:typeSF');
    $query->setParameters(['typeRomance'=>'Romance','typeSF'=>'Science-Fiction']);
  

    $query->getResult();
   }



   public function updateSfToRomanceWithQueryBuilder()
   {
    return $this->createQueryBuilder('b')
       ->update('App\Entity\Book', 'b')
           ->set('b.category', ':typeRomance')
           ->where('b.category = :typeSF')
           ->setParameter('typeRomance', 'Romance')
           ->setParameter('typeSF', 'Science-Fiction')
           ->getQuery()
           ->getResult();
       
   }


}
