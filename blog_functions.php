<?php
// ════════════════════════════════════════════════════
//  blog_functions.php — Kisken Trends Duuka
//  Requires: config/db.php (your existing file)
//  Uses:     $conn (your existing PDO connection)
// ════════════════════════════════════════════════════

// ── Safe output ──────────────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ── Format date ──────────────────────────────────────
function formatDate(string $date): string {
    return date('F j, Y', strtotime($date));
}

// ── Make URL slug from title ─────────────────────────
function makeSlug(string $title): string {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

// ════════════════════════════════════════════════════
//  AUTH HELPERS — matches your $_SESSION['user'] structure
// ════════════════════════════════════════════════════

function isLoggedIn(): bool {
    return !empty($_SESSION['user']) || !empty($_SESSION['user_id']);
}

function currentUserId(): ?int {
    if (!empty($_SESSION['user']['id']))  return (int)$_SESSION['user']['id'];
    if (!empty($_SESSION['user_id']))     return (int)$_SESSION['user_id'];
    return null;
}

function currentUserName(): string {
    if (!empty($_SESSION['user']['name'])) return $_SESSION['user']['name'];
    return 'User';
}

// Fetch logged-in user row from your existing users table
function getCurrentUser(PDO $conn): ?array {
    $id = currentUserId();
    if (!$id) return null;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function isAdmin($conn = null): bool {
    return ($_SESSION['user']['role'] ?? '') === 'admin';
}

// ════════════════════════════════════════════════════
//  POSTS
// ════════════════════════════════════════════════════

function getFeaturedPost(PDO $conn): ?array {
    $stmt = $conn->prepare("
        SELECT p.*, u.name AS author, c.name AS category, c.slug AS category_slug
        FROM blog_posts p
        JOIN users u ON u.id = p.author_id
        JOIN blog_categories c ON c.id = p.category_id
        WHERE p.status = 'published' AND p.is_featured = 1
        ORDER BY p.created_at DESC
        LIMIT 1
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function getRecentPosts(PDO $conn, int $limit = 3, int $excludeId = 0): array {
    $limit = (int)$limit;
    $stmt = $conn->prepare("
        SELECT p.*, u.name AS author, c.name AS category, c.slug AS category_slug
        FROM blog_posts p
        JOIN users u ON u.id = p.author_id
        JOIN blog_categories c ON c.id = p.category_id
        WHERE p.status = 'published' AND p.id != ?
        ORDER BY p.created_at DESC
        LIMIT $limit
    ");
    $stmt->execute([$excludeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostBySlug(PDO $conn, string $slug): ?array {
    $stmt = $conn->prepare("
        SELECT p.*, u.name AS author, c.name AS category, c.slug AS category_slug
        FROM blog_posts p
        JOIN users u ON u.id = p.author_id
        JOIN blog_categories c ON c.id = p.category_id
        WHERE p.slug = ? AND p.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function incrementViews(PDO $conn, int $postId): void {
    $conn->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")
         ->execute([$postId]);
}

function getPopularPosts(PDO $conn, int $limit = 3): array {
    $limit = (int)$limit;
    $stmt = $conn->prepare("
        SELECT id, title, slug,
               COALESCE(image_url, '') AS image_url,
               created_at, views
        FROM blog_posts
        WHERE status = 'published'
        ORDER BY views DESC
        LIMIT $limit
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoriesWithCount(PDO $conn): array {
    $stmt = $conn->query("
        SELECT c.id, c.name, c.slug, COUNT(p.id) AS post_count
        FROM blog_categories c
        LEFT JOIN blog_posts p ON p.category_id = c.id AND p.status = 'published'
        GROUP BY c.id, c.name, c.slug
        ORDER BY c.name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostsByCategory(PDO $conn, string $slug, int $limit = 9): array {
    $limit = (int)$limit;
    $stmt = $conn->prepare("
        SELECT p.*, u.name AS author, c.name AS category
        FROM blog_posts p
        JOIN users u ON u.id = p.author_id
        JOIN blog_categories c ON c.id = p.category_id
        WHERE c.slug = ? AND p.status = 'published'
        ORDER BY p.created_at DESC
        LIMIT $limit
    ");
    $stmt->execute([$slug]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── Helper: sanitise image_url — returns null when blank ──
function sanitiseImageUrl(?string $raw): ?string {
    if ($raw === null) return null;
    $url = trim($raw);
    return $url !== '' ? $url : null;
}

// Admin: save a new post
function savePost(PDO $conn, array $data): array {
function savePost(PDO $conn, array $data): array {
    try {
        $slug = makeSlug($data['title']);

        // Ensure slug is unique
        $base = $slug; $i = 1;
        while (true) {
            $chk = $conn->prepare("SELECT id FROM blog_posts WHERE slug = ? LIMIT 1");
            $chk->execute([$slug]);
            if (!$chk->fetch()) break;
            $slug = $base . '-' . $i++;
        }

        $stmt = $conn->prepare("
            INSERT INTO blog_posts
                (title, slug, excerpt, body, image_url, author_id, category_id, status, is_featured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            trim($data['title']),
            $slug,
            trim($data['excerpt']),
            trim($data['body']),
            sanitiseImageUrl($data['image_url'] ?? null),
            $data['author_id'],
            (int)$data['category_id'],
            $data['status']      ?? 'draft',
            (int)($data['is_featured'] ?? 0),
        ]);

        if (!$ok) {
            $err = $stmt->errorInfo();
            return ['ok' => false, 'error' => $err[2]];
        }

        return ['ok' => true, 'slug' => $slug, 'id' => (int)$conn->lastInsertId()];

    } catch (PDOException $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}
}

// Admin: update existing post
function updatePost(PDO $conn, int $id, array $data): void {
    $conn->prepare("
        UPDATE blog_posts
        SET title = ?, excerpt = ?, body = ?, image_url = ?,
            category_id = ?, status = ?, is_featured = ?, updated_at = NOW()
        WHERE id = ?
    ")->execute([
        trim($data['title']),
        trim($data['excerpt']),
        trim($data['body']),
        sanitiseImageUrl($data['image_url'] ?? null),   // FIX: null when empty
        (int)$data['category_id'],
        $data['status'] ?? 'draft',
        (int)($data['is_featured'] ?? 0),
        $id,
    ]);
}

// Admin: delete post
function deletePost(PDO $conn, int $id): void {
    $conn->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
}

// ════════════════════════════════════════════════════
//  COMMENTS
// ════════════════════════════════════════════════════

function getComments(PDO $conn, int $postId): array {
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS registered_name
        FROM blog_comments c
        LEFT JOIN users u ON u.id = c.user_id
        WHERE c.post_id = ? AND c.status = 'approved'
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommentCount(PDO $conn, int $postId): int {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM blog_comments WHERE post_id = ? AND status = 'approved'");
    $stmt->execute([$postId]);
    return (int)$stmt->fetchColumn();
}

function postComment(PDO $conn, array $data): array {
    $postId     = (int)($data['post_id']     ?? 0);
    $body       = trim($data['body']         ?? '');
    $userId     = $data['user_id']           ?? null;
    $guestName  = trim($data['guest_name']   ?? '');
    $guestEmail = trim($data['guest_email']  ?? '');

    if (!$postId)          return ['ok' => false, 'msg' => 'Invalid post.'];
    if (strlen($body) < 5) return ['ok' => false, 'msg' => 'Comment is too short (minimum 5 characters).'];

    if (!$userId) {
        if (empty($guestName))
            return ['ok' => false, 'msg' => 'Please enter your name.'];
        if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL))
            return ['ok' => false, 'msg' => 'Please enter a valid email address.'];
    }

    $conn->prepare("
        INSERT INTO blog_comments (post_id, user_id, guest_name, guest_email, body, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ")->execute([
        $postId,
        $userId ?: null,
        $userId ? null : $guestName,
        $userId ? null : $guestEmail,
        $body,
    ]);

    return ['ok' => true, 'msg' => 'Thanks! Your comment is awaiting approval.'];
}

// Admin: get all pending comments
function getPendingComments(PDO $conn): array {
    $stmt = $conn->query("
        SELECT c.*, p.title AS post_title, p.slug AS post_slug
        FROM blog_comments c
        JOIN blog_posts p ON p.id = c.post_id
        WHERE c.status = 'pending'
        ORDER BY c.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Admin: approve / spam / delete a comment
function updateCommentStatus(PDO $conn, int $commentId, string $status): void {
    if (!in_array($status, ['approved', 'spam', 'pending'], true)) return;
    $conn->prepare("UPDATE blog_comments SET status = ? WHERE id = ?")
         ->execute([$status, $commentId]);
}

function deleteComment(PDO $conn, int $commentId): void {
    $conn->prepare("DELETE FROM blog_comments WHERE id = ?")->execute([$commentId]);
}

// ════════════════════════════════════════════════════
//  NEWSLETTER
// ════════════════════════════════════════════════════

function subscribeEmail(PDO $conn, string $email): array {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        return ['ok' => false, 'msg' => 'Please enter a valid email address.'];

    $stmt = $conn->prepare("SELECT id, is_active FROM blog_subscribers WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if ($existing['is_active'])
            return ['ok' => false, 'msg' => 'You are already subscribed!'];
        $conn->prepare("UPDATE blog_subscribers SET is_active = 1 WHERE email = ?")->execute([$email]);
        return ['ok' => true, 'msg' => "Welcome back! You've been re-subscribed."];
    }

    $conn->prepare("INSERT INTO blog_subscribers (email) VALUES (?)")->execute([$email]);
    return ['ok' => true, 'msg' => "Subscribed! You'll get the latest footwear trends in your inbox."];
}

function getActiveSubscribers(PDO $conn): array {
    return $conn->query("SELECT * FROM blog_subscribers WHERE is_active = 1 ORDER BY subscribed_at DESC")
                ->fetchAll(PDO::FETCH_ASSOC);
}