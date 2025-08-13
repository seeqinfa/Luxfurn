<?php
/**
 * ChatbotEntity  –  contains:
 *   • getBotResponse()         → returns bot reply text
 *   • saveMessage()            → inserts one row
 *   • fetchRecentMessages()    → array of {sender, message_text}
 */
require_once dirname(__DIR__) . '/config.php';   // defines global $pdo

class ChatbotEntity
{
    private PDO $db;

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?? $GLOBALS['pdo'];
    }

    /* ---------- 1. Business logic (reply) ---------- */
    public function getBotResponse(string $input): string
    {
        $input = strtolower(trim($input));

        if ($input === '') {
            return "Say something and I'll try to help!";
        }
        if (str_contains($input, 'furniture')) {
            return 'Check the "View Furniture" page for our catalogue.';
        }
        if (str_contains($input, 'order')) {
            return 'Use the “My Orders ▾” menu to view your cart or order.';
        }
        return "ask me about furniture or orders.";
    }

    /* ---------- 2. Persistence helpers ---------- */
    public function saveMessage(
        string $username,
        string $sender,  // 'user' | 'bot'
        string $text
    ): void {
        $stmt = $this->db->prepare(
            "INSERT INTO chat_messages (username, sender, message_text)
             VALUES (:u, :s, :t)"
        );
        $stmt->execute([':u'=>$username, ':s'=>$sender, ':t'=>$text]);
    }

    public function fetchRecentMessages(string $username, int $limit = 100): array
	{
		$limit = max(1, (int)$limit);


		$sql = "
			SELECT sender, message_text, created_at
			FROM (
				SELECT sender, message_text, created_at
				FROM chat_messages
				WHERE username = :u
				ORDER BY created_at DESC
				LIMIT :lim
			) recent
			ORDER BY created_at ASC
		";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':u', $username, PDO::PARAM_STR);

		$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);

		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $rows;
	}

    public function listConversations(
        string $search = '',
        int $page = 1,
        int $perPage = 20,
        string $order = 'last'  // 'last' or 'started'
    ): array {
        $page = max(1, (int)$page);
        $perPage = min(max(5, (int)$perPage), 100);
        $offset = ($page - 1) * $perPage;

        // sanitize order
        $orderBy = ($order === 'started') ? 'started_at DESC' : 'last_at DESC';

        // search filter on username or message_text
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE (username LIKE :like OR message_text LIKE :like)";
            $params[':like'] = '%' . $search . '%';
        }

        // total conversations (grouped by username)
        $sqlTotal = "
            SELECT COUNT(*) AS c FROM (
                SELECT username
                FROM chat_messages
                $where
                GROUP BY username
            ) t
        ";
        $stmt = $this->db->prepare($sqlTotal);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        $total = (int)($stmt->fetchColumn() ?: 0);
        $stmt->closeCursor();

        // page of grouped rows
        $sql = "
            SELECT
                username,
                MIN(created_at) AS started_at,
                MAX(created_at) AS last_at,
                COUNT(*) AS msg_count
            FROM chat_messages
            $where
            GROUP BY username
            ORDER BY $orderBy
            LIMIT :offset, :limit
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return ['rows' => $rows, 'total' => $total];
    }

    /**
     * Fetch the full message thread for one "conversation" (by username),
     * ordered chronologically.
     *
     * Returns: array of rows [
     *   'sender'       => 'user'|'bot',
     *   'message_text' => string,
     *   'created_at'   => string
     * ]
     */
public function getConversationByUsername(string $username, int $limit = 100): array {
    // Pull most recent N, then present oldest -> newest
    $sql = "
        SELECT sender, message_text, created_at
        FROM (
            SELECT sender, message_text, created_at
            FROM chat_messages
            WHERE username = :u
            ORDER BY created_at DESC, sender DESC
            LIMIT :lim
        ) recent
        ORDER BY created_at ASC, sender ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':u', $username, PDO::PARAM_STR);

    // Depending on your PDO settings, binding LIMIT may require emulated prepares.
    // If you ever get a SQL error here, cast and inline:
    // $limit = (int)$limit; $sql = str_replace(':lim', $limit, $sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $rows;
}


}
