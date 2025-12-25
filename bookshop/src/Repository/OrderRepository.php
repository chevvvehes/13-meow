<?php
// src/Repository/OrderRepository.php
namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrdersWithAmounts(): array
    {
        return $this->getEntityManager()
            ->createQuery("
                SELECT o.id, o.createdAt, c.name as customer_name, SUM(oi.quantity * b.price) as total_amount
                FROM App\Entity\Order o
                JOIN App\Entity\Customer c WITH o.customer = c
                JOIN App\Entity\OrderItem oi WITH oi.order = o
                JOIN App\Entity\Book b WITH oi.book = b
                GROUP BY o.id, o.createdAt, c.name
                ORDER BY o.createdAt DESC
            ")
            ->getResult();
    }

    public function findAverageOrderAmount(): ?float
    {
        $result = $this->getEntityManager()
            ->createQuery("
                SELECT AVG(oi.quantity * b.price) as avg_amount
                FROM App\Entity\Order o
                JOIN App\Entity\OrderItem oi WITH oi.order = o
                JOIN App\Entity\Book b WITH oi.book = b
            ")
            ->getOneOrNullResult();

        return $result ? (float) $result['avg_amount'] : null;
    }
}