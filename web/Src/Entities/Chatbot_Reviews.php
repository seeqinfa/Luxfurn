<?php
// Src/Entities/Chatbot_Reviews.php
require_once dirname(__DIR__) . '/db_connect.php';

class Chatbot_Reviews
{
    public ?int $reviewID = null;
    public int  $user_id   = 0;
    public int  $rating    = 0; // 1..5
    public string $comment = '';
    public ?string $admin_comment = null;
    public string $created_at = '';

    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }

    /** Create new review */
    public static function create(int $user_id, int $rating, string $comment): bool
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO chatbot_reviews (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $user_id, $rating, $comment);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Fetch one by id */
    public static function findById(int $reviewID): ?array
    {
        global $conn;
        $sql = "
            SELECT cr.*, COALESCE(u.username, CONCAT('User #', cr.user_id)) AS username
            FROM chatbot_reviews cr
            LEFT JOIN users u ON u.id = cr.user_id
            WHERE cr.reviewID = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $reviewID);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Count all (simple, no filters) */
    public static function countAll(): int
    {
        global $conn;
        $res = $conn->query("SELECT COUNT(*) AS c FROM chatbot_reviews");
        $row = $res->fetch_assoc();
        return (int)$row['c'];
    }

    /** List with pagination (newest first) */
    public static function list(int $page = 1, int $perPage = 10): array
    {
        global $conn;
        $offset = max(0, ($page - 1) * $perPage);
        $sql = "
            SELECT cr.*, COALESCE(u.username, CONCAT('User #', cr.user_id)) AS username
            FROM chatbot_reviews cr
            LEFT JOIN users u ON u.id = cr.user_id
            ORDER BY cr.created_at DESC
            LIMIT ? OFFSET ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Update admin comment */
    public static function updateAdminComment(int $reviewID, string $admin_comment): bool
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE chatbot_reviews SET admin_comment=? WHERE reviewID=?");
        $stmt->bind_param('si', $admin_comment, $reviewID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Delete review */
    public static function delete(int $reviewID): bool
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM chatbot_reviews WHERE reviewID=?");
        $stmt->bind_param('i', $reviewID);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
