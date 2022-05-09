<?php

namespace App\Repository;

use App\Entity\Bien;
use App\Entity\User;
use App\Data\SearchData;
use Doctrine\ORM\ORMException;
use function PHPSTORM_META\type;
use Doctrine\ORM\Query\Expr\Select;
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
     * function recupere le bien par type ou par type de transaction
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
                ->setParameter('type', $search);
        }
        if ($search == 'Vente') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', $search);
        }
        return $query->getQuery()->getResult();
    }
    //retoure le nombre totale apres le trie
    public  function getCountTotalBienTrier(string $search)
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)');
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
                ->setParameter('type', $search);
        }
        if ($search == 'Vente') {
            $query->andWhere('a.transactionType = :type')
                ->setParameter('type', $search);
        }
        //getSingleScalarResult elle permet de retourner une resultat en type de base(chiffre,chaine...)
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * fuction filtre par prix(max,min) surface(max,min) prix(max,min)
     *@return array
     */
    public function getListefiltre(SearchData $search, int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('b')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
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

    //recuperer totol de bien de filtre
    public function getCoutListefiltre(SearchData $search): int
    {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b)');
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
     * fuction retourne le annance par page
     *@return void
     */
    public function getPaginationAnnonces($page, $limit)
    {

        $query = $this->createQueryBuilder('a')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }
    //retourne le nombre toltal de bien
    public  function getCountTotalBien()
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)');
        //getSingleScalarResult elle permet de retourner une resultat en type de base(chiffre,chaine...)
        return $query->getQuery()->getSingleScalarResult();
    }
    // public function getPaginationAnnoncesUser($page, $limit, User $user)
    // {

    //         $query = $this->createQueryBuilder('a')
    //         ->andWhere('a.user = :bienUser')
    //         ->setParameter('bienUser', $user->getId())
    //         ->setFirstResult(($page * $limit) - $limit)
    //         ->setMaxResults($limit);

    //     return $query->getQuery()->getResult();
    // }
    /**
     * fuction retourne les annances cree par agent immobilier
     *@return array
     */
    public function getannacesUser(User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.user = :bienUser')
            ->setParameter('bienUser', $user->getId());
        return $query->getQuery()->getResult();
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
