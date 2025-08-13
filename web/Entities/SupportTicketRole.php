<?php
// Src/Entities/SupportTicketRole.php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php'; // expects global $pdo (PDO)

class SupportTicketRoleRepo
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? $GLOBALS['pdo'];
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /** Fetch admins from users table (adjust query to your schema if needed). */
    public function fetchAdminUsers(): array
    {
        // If your schema uses a different indicator, tweak this WHERE
        $sql = "SELECT username, email
                  FROM users
                 WHERE role IN ('admin','superadmin')
              ORDER BY username ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Fetch currently active support agents. */
    public function fetchActiveAgents(): array
    {
        $sql = "SELECT username
                  FROM support_ticket_roles
                 WHERE active = 1
              ORDER BY username ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Set the exact list of usernames that should be active agents.
     * Inserts new, activates existing, deactivates the rest (transactional).
     */
    public function setActiveAgents(array $usernames, string $assignedBy): void
    {
        // Normalize input (unique, trimmed, non-empty)
        $usernames = array_values(array_unique(array_filter(array_map('strval', $usernames))));
        $this->db->beginTransaction();

        try {
            // 1) Upsert all selected as active
            if (!empty($usernames)) {
                $ins = $this->db->prepare(
                    "INSERT INTO support_ticket_roles (username, active, assigned_by)
                     VALUES (:u, 1, :by)
                     ON DUPLICATE KEY UPDATE
                         active=VALUES(active),
                         assigned_by=VALUES(assigned_by),
                         updated_at=CURRENT_TIMESTAMP"
                );
                foreach ($usernames as $u) {
                    $ins->execute([':u' => $u, ':by' => $assignedBy]);
                }
            }

            // 2) Deactivate anyone currently active but not in the selection
            if (!empty($usernames)) {
                // Build IN list placeholders
                $in  = implode(',', array_fill(0, count($usernames), '?'));
                $sql = "UPDATE support_ticket_roles
                           SET active = 0, assigned_by = ?
                         WHERE active = 1
                           AND username NOT IN ($in)";
                $stmt = $this->db->prepare($sql);
                $params = array_merge([$assignedBy], $usernames);
                $stmt->execute($params);
            } else {
                // If nothing selected, deactivate all
                $sql = "UPDATE support_ticket_roles
                           SET active = 0, assigned_by = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$assignedBy]);
            }

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
