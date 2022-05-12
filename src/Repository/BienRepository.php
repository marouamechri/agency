<?php

namespace App\Repository;

use App\Entity\Bien;
use App\Entity\User;
use App\Data\SearchData;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Bien>
 *
 * @method Bien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bien[]    findAll()
 * @method Bien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bien::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Bien $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Bien $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    /**
     * fonction permet de recuperer les bien diponible (en vente ou en location)
     * 
     * @param int $page
     * @param int $limit
     * @return array
     */

    public function getPaginationAnnonces(int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('a')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'A louer');

        return $query->getQuery()->getResult();
            
    }
    /**
     * retourne le nombre toltal de bien desponible(en vente ou location)
     *
     * @return void
     */
    public  function getCountTotalBien()
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'A louer');
        //getSingleScalarResult elle permet de retourner une resultat en type de base(chiffre,chaine...)
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * fuction retourne les annances cree par agent immobilier connecter
     * 
     * @param User $user
     *@return array
     */
    public function getannacesUser(UserInterface $user)
    {
        $query = $this->createQueryBuilder('a')
            ->join('a.user', 'b')
            ->andWhere('b = :bienUser')
            ->setParameter('bienUser', $user)
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'A louer');
        return $query->getQuery()->getResult();
    }

    /**
     * function permet de recuperer les annnaces close (qui son vendu eu louer) d'un user
     *
     * @param User $user
     * @return void
     */
    public function getannanceArchiverUser(UserInterface $user)
    {
        $query = $this->createQueryBuilder('a')
            ->join('a.user', 'b')
            ->andWhere('b = :bienUser')
            ->setParameter('bienUser', $user)
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'Vendu')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'Louer');

        return $query->getQuery()->getResult();
    }

    /**
     * fonction permet de recuperer tous les biens disponible
     *
     * @return array
     */
    public function getAllBienDispo(): array
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'A louer');
        return $query->getQuery()->getResult();

    } 
    /**
     * fonction permet de recuperer les bien disponible 
     *
     * @return array
     */
    public function getAllBienArchive(): array
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'Vendu')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'louer');
        return $query->getQuery()->getResult();
    }
    
    /**
     * function recupere le bien par type ou par type de transaction
     * @param string $search
     * @param int $page
     * @param int $limit
     *@return array
     */
    public function getlisttrie(string $search, int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('a')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        if ($search == 'Appartement') {
            $query->andWhere('a.type = :type')
                ->setParameter('type', $search);
        }
        if ($search == 'Maison') {
            $query->andWhere('a.type = :type')
                ->setParameter('type', $search);
        }
        if ($search == 'Location') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', 'A vendre');
        }
        if ($search == 'Vente') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', 'A louer');
        }
        return $query->getQuery()->getResult();
    }
    /**
     * retoure le nombre totale de bien disponibel(en vente et location) aprés le trie
     *
     * @param string $search
     * @return void
     */

    public  function getCountTotalBienTrier(string $search)
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->andWhere('a.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('a.transactionType = :louer')
            ->setParameter('louer', 'A louer');
        if ($search == 'Appartement') {
            $query->andWhere('a.type = :type')
                ->setParameter('type', $search);
        }
        if ($search == 'Maison') {
            $query->andWhere('a.type = :type')
                ->setParameter('type', $search);
        }
        if ($search == 'Location') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', 'A louer');
        }
        if ($search == 'Vente') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', 'A vendre');
        }
        //getSingleScalarResult elle permet de retourner une resultat en type de base(chiffre,chaine...)
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * fuction filtre par prix(max,min) surface(max,min) prix(max,min) sur les bien disponible(en vente ou location)
     * @param SearchData $search
     * @param int $page
     * @param int $limit
     *@return array
     */
    public function getListefiltre(SearchData $search, int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('b')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->andWhere('b.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('b.transactionType = :louer')
            ->setParameter('louer', 'A louer');
            
        if (!empty($search->nbpiecemax)) {
            $query
                ->andWhere('b.nbPiece <= :maxPiece')
                ->setParameter('maxPiece', $search->nbpiecemax);
        }
        if (!empty($search->nbpiecemin)) {
            $query
                ->andWhere('b.nbPiece >= :minPiece')
                ->setParameter('minPiece', $search->nbpiecemin);
        }
        if (!empty($search->surfacemax)) {
            $query
                ->andWhere('b.surface <= :maxSurface')
                ->setParameter('maxSurface', $search->surfacemax);
        }
        if (!empty($search->surfacemin)) {
            $query
                ->andWhere('b.surface >= :minSurface')
                ->setParameter('minSurface', $search->surfacemin);
        }
        if (!empty($search->prixmax)) {
            $query
                ->andWhere('b.prix <= :maxPrix')
                ->setParameter('maxPrix', $search->prixmax);
        }
        if (!empty($search->prixmin)) {
            $query
                ->andWhere('b.prix >= :minPrix')
                ->setParameter('minPrix', $search->prixmin);
        }

        return $query->getQuery()->getResult();
    }

    
    /**
     * recuperer count totol de bien ( en vente set location)aprés le filtre
     *
     * @param SearchData $search
     * @return integer
     */
    public function getCoutListefiltre(SearchData $search): int
    {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b)')
            ->andWhere('b.transactionType = :vente')
            ->setParameter('vente', 'A vendre')
            ->orWhere('b.transactionType = :louer')
            ->setParameter('louer', 'A louer');
        if (!empty($search->nbpiecemax)) {
            $query
                ->andWhere('b.nbPiece <= :maxPiece')
                ->setParameter('maxPiece', $search->nbpiecemax);
        }
        if (!empty($search->nbpiecemin)) {
            $query
                ->andWhere('b.nbPiece >= :minPiece')
                ->setParameter('minPiece', $search->nbpiecemin);
        }
        if (!empty($search->surfacemax)) {
            $query
                ->andWhere('b.surface <= :maxSurface')
                ->setParameter('maxSurface', $search->surfacemax);
        }
        if (!empty($search->surfacemin)) {
            $query
                ->andWhere('b.surface >= :minSurface')
                ->setParameter('minSurface', $search->surfacemin);
        }
        if (!empty($search->prixmax)) {
            $query
                ->andWhere('b.prix <= :maxPrix')
                ->setParameter('maxPrix', $search->prixmax);
        }
        if (!empty($search->prixmin)) {
            $query
                ->andWhere('b.prix >= :minPrix')
                ->setParameter('minPrix', $search->prixmin);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * fonction permet d'ordonner les biens par nembre piéce (asc ou desc) 
     *
     * @param [type] $orderPieces
     * @param [type] $page
     * @param [type] $limit
     * @return array
     */
    public function orderPieces(string $orderPieces, int $page, int $limit):array
    {
        $qb = $this->createQueryBuilder('b')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        if($orderPieces == 'DESC')
        {
            $qb->select('b')
                ->orderBy('b.nbPiece', $orderPieces) ;
            $orderPieces = 'ASC';
        }
        else{
            $qb->select('b')
            ->orderBy('b.nbPiece', $orderPieces) ;
            $orderPieces = 'DESC';
        }

        return $qb->getQuery()->getResult();

    }
    /**
     * function permet de ordonner les biens par (asc ou desc)surface
     *
     * @param string $orderSurface
     * @param integer $page
     * @param integer $limit
     * @return array
     */
    public function orderSurfaces(string $orderSurface, int $page, int $limit):array
    {
        $qb = $this->createQueryBuilder('b')
        ->setFirstResult(($page * $limit) - $limit)
        ->setMaxResults($limit);

    if($orderSurface == 'DESC')
    {
        $qb->select('b')
            ->orderBy('b.surface', $orderSurface) ;
        $orderSurface = 'ASC';
    }
    else{
        $qb->select('b')
        ->orderBy('b.surface', $orderSurface) ;
        $orderSurface = 'DESC';
    }

    return $qb->getQuery()->getResult();

    }
    /**
     * fonction permet de ordonner les biens (asc ou desc) prix
     *
     * @param string $orderPrix
     * @param integer $page
     * @param integer $limit
     * @return array
     */
    public function orderPrix(string $orderPrix,int $page, int $limit):array
    {
        $qb = $this->createQueryBuilder('b')
        ->setFirstResult(($page * $limit) - $limit)
        ->setMaxResults($limit);

    if($orderPrix == 'DESC')
    {
        $qb->select('b')
            ->orderBy('b.surface', $orderPrix) ;
        $orderPrix = 'ASC';
    }
    else{
        $qb->select('b')
        ->orderBy('b.surface', $orderPrix) ;
        $orderPrix = 'DESC';
    }

    return $qb->getQuery()->getResult();

    }
    
   
    // /**
    //  * @return Bien[] Returns an array of Bien objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bien
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
