<?php
// src/Repository/CustomerRepository.php
namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findCustomerOrderCounts(): array
    {
        return $this->getEntityManager()
            ->createQuery("
                SELECT c.name, c.email, COUNT(o.id) as order_count
                FROM App\Entity\Customer c
                LEFT JOIN App\Entity\Order o WITH c = o.customer
                GROUP BY c.id, c.name, c.email
                ORDER BY order_count DESC
            ")
            ->getResult();
    }

    public function findTopCustomersByTotalSpent(int $limit): array
    {
        return $this->getEntityManager()
            ->createQuery("
                SELECT c.name, c.email, SUM(oi.quantity * b.price) as total_spent
                FROM App\Entity\Customer c
                JOIN App\Entity\Order o WITH c = o.customer
                JOIN App\Entity\OrderItem oi WITH oi.order = o
                JOIN App\Entity\Book b WITH oi.book = b
                GROUP BY c.id, c.name, c.email
                ORDER BY total_spent DESC
            ")
            ->setMaxResults($limit)
            ->getResult();
    }
}