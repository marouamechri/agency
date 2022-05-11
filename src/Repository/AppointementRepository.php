<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Appointement;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Appointement>
 *
 * @method Appointement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointement[]    findAll()
 * @method Appointement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointement::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Appointement $entity, bool $flush = true): void
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
    public function remove(Appointement $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
     /**
     * fuction retourne les rendevous par employer
     *@return array
     */
    public function getAppointement(User $user):array
    {

        $query = $this->createQueryBuilder('a')

            ->join('a.titre', 'b')

            ->andWhere('b.user = :val')

            ->setParameter('val', $user);

            ;

        return $query->getQuery()->getResult();

    }
    // /**
    //  * @return Appointement[] Returns an array of Appointement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Appointement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
