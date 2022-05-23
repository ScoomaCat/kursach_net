<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Appointment $entity, bool $flush = true): void
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
    public function remove(Appointment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findForCurrentWeek(User $master): array
    {
        $result = [];
        $weekStart = new \DateTime('monday this week');
        $weekEnd = new \DateTime('saturday this week');

        $period = new \DatePeriod(
            $weekStart,
            new \DateInterval('P1D'),
            $weekEnd
        );

        foreach ($period as $key => $value) {
            $result[$value->format('Y-m-d')] = [];
        }

        $appointments = $this->createQueryBuilder('a')
            ->andWhere('a.date >= :date')
            ->andWhere('a.date <= :dateEnd')
            ->setParameter('date', $weekStart)
            ->setParameter('dateEnd', $weekEnd)
            ->andWhere('a.master = :master')
            ->setParameter('master', $master)
            ->orderBy('a.date', Criteria::DESC)
            ->getQuery()
            ->getResult();

        foreach ($appointments as $appointment) {
            $result[$appointment->getDate()->format('Y-m-d')][] = $appointment;
        }

        return $result;
    }

    /*
    public function findOneBySomeField($value): ?Appointment
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
