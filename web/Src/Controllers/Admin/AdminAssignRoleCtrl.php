<?php
// Src/Controllers/Admin/AdminAssignRoleCtrl.php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Entities/SupportTicketRole.php';

class AdminAssignRoleCtrl
{
    private SupportTicketRoleRepo $repo;

    public function __construct(?PDO $pdo = null)
    {
        $this->repo = new SupportTicketRoleRepo($pdo);
    }

    public function getAdmins(): array
    {
        return $this->repo->fetchAdminUsers();
    }

    /** Returns array ['username' => true, ...] for easy checkbox binding. */
    public function getActiveMap(): array
    {
        $rows = $this->repo->fetchActiveAgents();
        $map = [];
        foreach ($rows as $r) { $map[$r['username']] = true; }
        return $map;
    }

    public function saveAssignments(array $selected, string $assignedBy): void
    {
        $this->repo->setActiveAgents($selected, $assignedBy);
    }
}
