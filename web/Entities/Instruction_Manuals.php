<?php
// Src/Entities/Instruction_Manuals.php
require_once dirname(__DIR__) . '/db_connect.php';

class instruction_manuals
{
    /**
     * Search manuals by product name / code / keywords with pagination.
     * @return array [$rows, $total]
     */
    public static function search(string $q = '', int $page = 1, int $perPage = 10): array
    {
        global $conn;

        $q = trim($q);
        $offset = max(0, ($page - 1) * $perPage);

        // Build WHERE + params safely
        $where = '';
        $params = [];
        $types  = '';

        if ($q !== '') {
            $where  = 'WHERE product_name LIKE ? OR product_code LIKE ? OR keywords LIKE ?';
            $needle = '%' . $q . '%';
            $params = [$needle, $needle, $needle];
            $types  = 'sss';
        }

        // Count
        $sqlCount = "SELECT COUNT(*) AS total FROM instruction_manuals $where";
        $stmt = $conn->prepare($sqlCount);
        if ($types) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        $total = 0;
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        // List
        $sql = "SELECT manualID, product_name, product_code, manual_url, updated_at
                FROM instruction_manuals
                $where
                ORDER BY updated_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if ($types) {
            $stmt->bind_param($types . 'ii', ...array_merge($params, [$perPage, $offset]));
        } else {
            $stmt->bind_param('ii', $perPage, $offset);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return [$rows, (int)$total];
    }
}
