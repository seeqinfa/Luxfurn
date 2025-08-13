<?php
// Src/Boundary/Admin/AdminViewChatbotUI.php
require_once '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Admin/AdminViewChatCtrl.php';

if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$ctrl = new AdminViewChatCtrl();

// Query params
$q      = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$pp     = (int)($_GET['pp'] ?? 10);
if ($pp < 5)   $pp = 10;
if ($pp > 100) $pp = 100;

$sort   = $_GET['sort'] ?? 'last'; // 'last' or 'started'
$user   = trim($_GET['user'] ?? ''); // drill-down key (username)

// Data
$list   = $ctrl->listConversations($q, $page, $pp, $sort);
$rows   = $list['rows'] ?? [];
$total  = (int)($list['total'] ?? 0);
$pages  = max(1, (int)ceil($total / max(1, $pp)));

// Drill-down
$messages = [];
if ($user !== '') {
    $messages = $ctrl->getConversationMessages($user);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatbot Conversations</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Keep page width stable even when vertical scrollbar toggles */
        :root { scrollbar-gutter: stable both-edges; }
        @supports not (scrollbar-gutter: stable) {
          html { overflow-y: scroll; } /* fallback */
        }

        .container { margin-top: 140px; max-width: 1200px; width:100%; padding: 0 20px; margin-inline: auto; }
        .section-title { font-size: 24px; margin: 30px 0 20px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }

        .searchbar { display:flex; gap:10px; margin-bottom:20px; flex-wrap: wrap; }
        .searchbar input, .searchbar select { padding:10px; border:1px solid #ccc; border-radius:4px; font-size:14px; }
        .searchbar button { padding:10px 16px; background:#e67e22; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .searchbar button:hover { background:#d35400; }
        .btn { display:inline-block; padding:8px 12px; border-radius:6px; background:#f4f4f4; color:#333; text-decoration:none; }
        .btn:hover { background:#e9e9e9; }

        /* Fixed-size conversation list box */
        .listbox {
          background:#fff;
          border-radius:8px;
          box-shadow:0 4px 10px rgba(0,0,0,.1);
          min-height: 360px;
          max-height: 360px;
          display:flex;
          flex-direction:column;
          overflow:hidden;
          margin-bottom: 16px;
        }
        .list-scroll {
          flex:1;
          overflow:auto;       /* table scrolls inside */
        }
        .list-pager {
          border-top:1px solid #eee;
          padding:8px;
          text-align:center;
          min-height:44px;     /* constant height */
          display:flex;
          align-items:center;
          justify-content:center;
          background:#fafafa;
        }

        /* Stable-width table */
        table.convos { width:100%; border-collapse: collapse; background:#fff; table-layout: fixed; }
        table.convos th, table.convos td { padding:12px 15px; text-align:left; border-bottom:1px solid #eee; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        table.convos th { background:#e67e22; color:#fff; position: sticky; top: 0; z-index: 1; }
        table.convos tr:nth-child(even) { background:#fafafa; }

        .muted { color:#666; font-size:13px; }
        .pill { display:inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; background: #f0f0f0; }
        .actions a { text-decoration:none; color:#3498db; }

        .pagination a { display:inline-block; padding:8px 14px; margin:0 4px; background:#f4f4f4; color:#333; text-decoration:none; border-radius:4px; }
        .pagination a.active { background:#e67e22; color:#fff; }
        .pagination a:hover:not(.active) { background:#ddd; }

        /* Compact chat styles */
        .chatbox {
          margin-top: 16px;
          background: #fff;
          border-radius: 10px;
          box-shadow: 0 3px 10px rgba(0,0,0,.08);
          padding: 12px;
        }
        .chatbox .toolbar {
          display: flex;
          gap: 8px;
          align-items: center;
          margin-bottom: 8px;
        }
        .chat-scroll {
          max-height: 540px;           /* adjust height if needed */
          overflow: auto;
          padding-right: 4px;
        }
        .chatlist {
          list-style: none;
          padding: 0;
          margin: 0;
          display: grid;
          gap: 6px;                     /* tight spacing between messages */
        }
        .row { display: flex; }
        .row.user { justify-content: flex-end; }
        .row.bot  { justify-content: flex-start; }

        .msg {
          display: inline-block;
          max-width: 720px;             /* cap width for readability */
          border-radius: 12px;
          padding: 8px 10px;            /* compact padding */
          line-height: 1.35;
          font-size: 13px;              /* smaller text */
          word-wrap: break-word;
          white-space: pre-wrap;
          border: 1px solid transparent;
          position: relative;
        }
        .msg.user { background: #eaf4ff; border-color: #cfe6ff; }
        .msg.bot  { background: #fff6e9; border-color: #f5d9ab; }

        .meta {
          font-size: 11px;
          color: #7a7a7a;
          margin-bottom: 3px;
          display: flex;
          gap: 8px;
          align-items: baseline;
        }
        .meta .who { font-weight: 600; color: #444; }
        .meta .time { opacity: .85; }

        @media (max-width: 768px) {
          .msg { max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="section-title"><i class="fas fa-robot"></i> Chatbot Conversations</h1>

    <form class="searchbar" method="get" action="">
        <input type="text" name="q" placeholder="Search username or message…" value="<?= h($q) ?>">
        <select name="sort">
            <option value="last"    <?= $sort==='last'?'selected':''; ?>>Sort by last activity</option>
            <option value="started" <?= $sort==='started'?'selected':''; ?>>Sort by start time</option>
        </select>
        <select name="pp">
            <option value="10"  <?= $pp==10?'selected':''; ?>>10</option>
            <option value="20"  <?= $pp==20?'selected':''; ?>>20</option>
            <option value="50"  <?= $pp==50?'selected':''; ?>>50</option>
            <option value="100" <?= $pp==100?'selected':''; ?>>100</option>
        </select>
        <button type="submit"><i class="fas fa-search"></i> Search</button>
    </form>

    <!-- Fixed-size Conversations list -->
    <div class="listbox">
      <div class="list-scroll">
        <table class="convos">
          <!-- Fixed column widths to prevent layout shift -->
          <colgroup>
            <col style="width:28%">
            <col style="width:12%">
            <col style="width:24%">
            <col style="width:24%">
            <col style="width:12%">
          </colgroup>
          <thead>
            <tr>
              <th>Username</th>
              <th>Messages</th>
              <th>Started</th>
              <th>Last Activity</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="5" class="muted">No conversations found.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><span class="pill"><?= h($r['username']) ?></span></td>
              <td><?= (int)$r['msg_count'] ?></td>
              <td><span class="muted"><?= h($r['started_at']) ?></span></td>
              <td><span class="muted"><?= h($r['last_at']) ?></span></td>
              <td class="actions">
                <a href="?q=<?= urlencode($q) ?>&sort=<?= urlencode($sort) ?>&pp=<?= (int)$pp ?>&page=<?= (int)$page ?>&user=<?= urlencode($r['username']) ?>">
                  View
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Constant-height pager area (keeps box size the same) -->
      <div class="list-pager">
        <?php if ($pages > 1 && $user === ''): ?>
          <div class="pagination">
            <?php for ($p=1; $p<=$pages; $p++): ?>
              <a class="<?= $p===$page ? 'active':''; ?>"
                 href="?q=<?= urlencode($q) ?>&sort=<?= urlencode($sort) ?>&pp=<?= (int)$pp ?>&page=<?= $p ?>">
                <?= $p ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php else: ?>
          <!-- placeholder to preserve height -->
          <span class="muted" style="visibility:hidden;">pagination</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Conversation drill-down -->
    <?php if ($user !== ''): ?>
        <div class="chatbox">
            <div class="toolbar">
                <strong>Conversation with:</strong>
                <span class="pill"><?= h($user) ?></span>
            </div>

            <div class="chat-scroll" id="chat-scroll">
                <?php if (!$messages): ?>
                    <div class="muted">No messages for this user.</div>
                <?php else: ?>
                    <ul class="chatlist">
                        <?php foreach ($messages as $m):
                            $isUser = strtolower((string)$m['sender']) === 'user';
                            $who    = $isUser ? 'User' : 'Bot';
                            $rowCls = $isUser ? 'user' : 'bot';
                            $msgCls = $isUser ? 'user' : 'bot';
                        ?>
                          <li class="row <?= $rowCls ?>">
                            <div class="msg <?= $msgCls ?>">
                              <div class="meta">
                                <span class="who"><?= h($who) ?></span>
                                <span class="time"><?= h($m['created_at']) ?></span>
                              </div>
                              <div class="text"><?= nl2br(h($m['message_text'])) ?></div>
                            </div>
                          </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <script>
            (function(){
              var sc = document.getElementById('chat-scroll');
              if (sc) sc.scrollTop = sc.scrollHeight;
            })();
        </script>
    <?php endif; ?>
</div>
</body>
</html>
