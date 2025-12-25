<?php
// src/Controller/BookshopController.php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookshopController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(CustomerRepository $customerRepo, OrderRepository $orderRepo, BookRepository $bookRepo): Response
    {
        // 1. Список людей и сколько раз покупали
        $customerOrderCounts = $customerRepo->findCustomerOrderCounts();

        // 2. Сумма по каждому заказу
        $ordersWithAmounts = $orderRepo->findOrdersWithAmounts();

        // 3. Топ-3 клиента по общей сумме
        $topCustomers = $customerRepo->findTopCustomersByTotalSpent(3);

        // 4. Средняя сумма заказа
        $averageOrderAmount = $orderRepo->findAverageOrderAmount();

        // 5. Самая дорогая книга
        $mostExpensiveBook = $bookRepo->findMostExpensiveBook();

        return $this->render('bookshop/index.html.twig', [
            'customer_order_counts' => $customerOrderCounts,
            'orders_with_amounts' => $ordersWithAmounts,
            'top_customers' => $topCustomers,
            'average_order_amount' => $averageOrderAmount,
            'most_expensive_book' => $mostExpensiveBook,
        ]);
    }
}