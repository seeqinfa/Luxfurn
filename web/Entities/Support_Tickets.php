<?php
// Src/Entities/SupportTicket.php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php'; // exposes $pdo

final class SupportTicketRepo
{
    private PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if (!$this->db instanceof PDO) {
            throw new RuntimeException('Database connection not initialized.');
        }
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /* ---------- Queries ---------- */

    public function listTickets(?string $status = null): array
    {
        if ($status) {
            $st = $this->db->prepare(
                "SELECT id, user_id, assigned_admin_id, subject, message, status, created_at, updated_at
                   FROM support_tickets
                  WHERE status = ?
                  ORDER BY created_at DESC"
            );
            $st->execute([$status]);
            return $st->fetchAll();
        }
        return $this->db->query(
            "SELECT id, user_id, assigned_admin_id, subject, message, status, created_at, updated_at
               FROM support_tickets
              ORDER BY created_at DESC"
        )->fetchAll();
    }

    public function getTicket(int $ticketId): ?array
    {
        $st = $this->db->prepare(
            "SELECT id, user_id, assigned_admin_id, subject, message, status, created_at, updated_at
               FROM support_tickets
              WHERE id = ?"
        );
        $st->execute([$ticketId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function getReplies(int $ticketId): array
    {
        $st = $this->db->prepare(
            "SELECT id, ticket_id, admin_id, message, created_at
               FROM support_ticket_replies
              WHERE ticket_id = ?
              ORDER BY created_at ASC, id ASC"
        );
        $st->execute([$ticketId]);
        return $st->fetchAll();
    }

    /* ---------- Mutations ---------- */

    public function addReply(int $ticketId, ?int $adminId, string $message): void
    {
        $st = $this->db->prepare(
            "INSERT INTO support_ticket_replies (ticket_id, admin_id, message)
             VALUES (?, ?, ?)"
        );
        $st->execute([$ticketId, $adminId, $message]);
    }

    public function bumpStatusAfterReply(int $ticketId): void
    {
        $st = $this->db->prepare(
            "UPDATE support_tickets
                SET status = CASE WHEN status <> 'resolved' THEN 'responded' ELSE status END,
                    updated_at = CURRENT_TIMESTAMP
              WHERE id = ?"
        );
        $st->execute([$ticketId]);
    }

    public function resolve(int $ticketId): void
    {
        $st = $this->db->prepare(
            "UPDATE support_tickets
                SET status = 'resolved', updated_at = CURRENT_TIMESTAMP
              WHERE id = ?"
        );
        $st->execute([$ticketId]);
    }
    public function getTicketsForAdmin(int $adminId): array
    {
        $st = $this->db->prepare(
            "SELECT id, user_id, assigned_admin_id, subject, message, status, created_at, updated_at
            FROM support_tickets
            WHERE assigned_admin_id = ?
            ORDER BY created_at DESC"
        );
        $st->execute([$adminId]);
        return $st->fetchAll();
    }
    public function getTicketsForUser(int $userId): array
    {
        $st = $this->db->prepare(
            "SELECT id, user_id, assigned_admin_id, subject, message, status, created_at, updated_at
            FROM support_tickets
            WHERE user_id = ?
            ORDER BY created_at DESC"
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }
}
