<?php
// Src/Controllers/AdminRatingandReivewsCtrl.php
require_once dirname(__DIR__, 2) . '/Entities/Chatbot_Reviews.php';

class AdminRatingandReivewsCtrl
{
    /** For Boundary list view */
    public function list(int $page = 1, int $perPage = 10): array
    {
        $rows  = Chatbot_Reviews::list($page, $perPage);
        $total = Chatbot_Reviews::countAll();
        return [$rows, $total];
    }

    /** Handle POST actions from Boundary */
    public function handlePost(): void
    {
        $action   = $_POST['action'] ?? '';
        $reviewID = (int)($_POST['reviewID'] ?? 0);

        if (!$action || $reviewID <= 0) {
            $this->redirectBack();
            return;
        }

        if ($action === 'save_admin_comment') {
            $admin_comment = trim($_POST['admin_comment'] ?? '');
            Chatbot_Reviews::updateAdminComment($reviewID, $admin_comment);
        } elseif ($action === 'delete') {
            Chatbot_Reviews::delete($reviewID);
        }

        $this->redirectBack();
    }

    private function redirectBack(): void
    {
        // Return to Boundary page
        header("Location: ../../Boundary/Admin/AdminRatingandReviewsUI.php");
        exit;
    }
}

// If posted directly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AdminRatingandReivewsCtrl())->handlePost();
}
