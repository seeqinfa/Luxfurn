<?php
// Src/Controllers/Admin/AdminViewChatCtrl.php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Entities/ChatbotEntity.php';

class AdminViewChatCtrl
{
    /** @var ChatbotEntity */
    private ChatbotEntity $repo;

    public function __construct()
    {
        // ChatbotEntity uses global $pdo from src/config.php
        $this->repo = new ChatbotEntity();
    }

    /**
     * List "conversations" grouped by username.
     * $order: 'last' or 'started'
     */
    public function listConversations(string $search, int $page, int $perPage, string $order): array
    {
        // Delegate to entity helpers you added earlier
        return $this->repo->listConversations($search, $page, $perPage, $order);
    }

    /**
     * Fetch full thread by username (chronological).
     */
    public function getConversationMessages(string $username, int $limit = 1000): array
    {
        return $this->repo->getConversationByUsername($username, $limit);
    }
}
