<?php
// Src/Controllers/Admin/AdminSupportTicketsCtrl.php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Entities/Support_Tickets.php';

final class AdminSupportTicketsCtrl
{
    private SupportTicketRepo $repo;

    public function __construct(?PDO $pdo = null)
    {
        $this->repo = new SupportTicketRepo($pdo);
    }

    public function listTickets(?string $status = null): array
    {
        return $this->repo->listTickets($status);
    }

    public function getTicketDetails(int $id): ?array
    {
        return $this->repo->getTicket($id);
    }

    public function getTicketReplies(int $id): array
    {
        return $this->repo->getReplies($id);
    }

    public function respondToTicket(int $ticketId, ?int $adminId, string $message): void
    {
        $message = trim($message);
        if ($message === '') {
            throw new InvalidArgumentException('Response cannot be empty.');
        }
        if (mb_strlen($message) > 2000) {
            $message = mb_substr($message, 0, 2000);
        }

        $t = $this->repo->getTicket($ticketId);
        if (!$t) {
            throw new RuntimeException('Ticket not found.');
        }
        if (strtolower((string)$t['status']) === 'resolved') {
            throw new RuntimeException('Ticket already resolved.');
        }

        $this->repo->addReply($ticketId, $adminId, $message);
        $this->repo->bumpStatusAfterReply($ticketId);
    }

    public function resolveTicket(int $ticketId): void
    {
        $t = $this->repo->getTicket($ticketId);
        if (!$t) {
            throw new RuntimeException('Ticket not found.');
        }
        if (strtolower((string)$t['status']) === 'resolved') {
            return; // idempotent
        }
        $this->repo->resolve($ticketId);
    }
    public function getTicketsForAdmin(int $adminId): array
    {
        // pass-through to repo
        if (!method_exists($this->repo, 'getTicketsForAdmin')) {
            throw new RuntimeException('Repository is missing getTicketsForAdmin().');
        }
        return $this->repo->getTicketsForAdmin($adminId);
    }
}
