<?php
// Src/Controllers/viewOrderCtrl.php
require_once dirname(__DIR__,2) . '/Entities/Order.php';

class ViewOrderCtrl
{
    public function showForUser(int $orderId, string $username): ?array
    {
        return Order::getByIdForUser($orderId, $username);
    }
    public function listForUser(string $username, int $limit = 50, int $offset = 0): array
    {
        return Order::listForUser($username, $limit, $offset);
    }
    public function cancelForUser(int $orderId, string $username): bool
    {
        return Order::cancelForUser($orderId, $username);
    }
}