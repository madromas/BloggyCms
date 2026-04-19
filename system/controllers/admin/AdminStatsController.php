<?php

/**
* Контроллер для получения статистических данных через AJAX
* @package controllers\admin
*/
class AdminStatsController extends Controller {
    
    private $postModel;
    private $commentModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->postModel = new PostModel($db);
        $this->commentModel = new CommentModel($db);
    }

    protected $controllerInfo = [
        'name' => 'Панель управления',
        'author' => 'BloggyCMS', 
        'version' => '1.0.0',
        'has_settings' => true,
        'description' => 'Управление админ-панелью, блоками статистики и многим другим'
    ];
    
    /**
    * Получение данных для графиков
    */
    public function getStatsDataAction() {
        if (ob_get_level()) ob_clean();
        
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        
        $type = $_GET['type'] ?? 'publications';
        $period = $_GET['period'] ?? 'month';
        
        $data = [];
        
        try {
            switch ($type) {
                case 'publications':
                    $data = $this->getPublicationsStats($period);
                    break;
                case 'popular':
                    $data = $this->getPopularPostsStats();
                    break;
                case 'liked':
                    $data = $this->getLikedPostsStats();
                    break;
                case 'comments':
                    $data = $this->getCommentsStats($period);
                    break;
                case 'summary':
                    $data = $this->getSummaryStats();
                    break;
                default:
                    $data = ['error' => 'Неизвестный тип статистики'];
            }
        } catch (Exception $e) {
            $data = ['error' => $e->getMessage()];
        }
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
    * Статистика публикаций по дням/месяцам/годам
    */
    private function getPublicationsStats($period) {
        $labels = [];
        $data = [];
        
        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d.m', strtotime($date));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM posts 
                        WHERE DATE(created_at) = ? AND status = 'published'",
                        [$date]
                    );
                    $data[] = (int)$count;
                }
                break;
                
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d.m', strtotime($date));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM posts 
                        WHERE DATE(created_at) = ? AND status = 'published'",
                        [$date]
                    );
                    $data[] = (int)$count;
                }
                break;
                
            case 'quarter':
                for ($i = 12; $i >= 0; $i--) {
                    $weekStart = date('Y-m-d', strtotime("-$i weeks"));
                    $weekEnd = date('Y-m-d', strtotime("-$i weeks +6 days"));
                    $labels[] = date('d.m', strtotime($weekStart)) . ' - ' . date('d.m', strtotime($weekEnd));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM posts 
                        WHERE created_at BETWEEN ? AND ? AND status = 'published'",
                        [$weekStart, $weekEnd . ' 23:59:59']
                    );
                    $data[] = (int)$count;
                }
                break;
                
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    $labels[] = date('M Y', strtotime($month));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM posts 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'published'",
                        [$month]
                    );
                    $data[] = (int)$count;
                }
                break;
                
            case 'all':
            default:
                $months = $this->db->fetchAll(
                    "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                    FROM posts 
                    WHERE status = 'published'
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 24"
                );
                
                foreach ($months as $month) {
                    $labels[] = date('M Y', strtotime($month['month'] . '-01'));
                    $data[] = (int)$month['count'];
                }
                break;
        }
        
        $total = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM posts WHERE status = 'published'"
        );
        
        $thisMonth = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM posts 
            WHERE MONTH(created_at) = MONTH(NOW()) 
            AND YEAR(created_at) = YEAR(NOW())
            AND status = 'published'"
        );
        
        $lastMonth = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM posts 
            WHERE MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH)
            AND YEAR(created_at) = YEAR(NOW() - INTERVAL 1 MONTH)
            AND status = 'published'"
        );
        
        $trend = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100, 1) : ($thisMonth > 0 ? 100 : 0);
        
        return [
            'labels' => $labels,
            'data' => $data,
            'total' => $total,
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'trend' => $trend
        ];
    }
    
    /**
    * Топ популярных постов (по просмотрам)
    */
    private function getPopularPostsStats() {
        $limit = (int)($_GET['limit'] ?? 5);
        $limit = max(1, min(20, $limit));
        
        $posts = $this->db->fetchAll(
            "SELECT id, title, slug, views, featured_image, created_at
            FROM posts 
            WHERE status = 'published' AND views > 0
            ORDER BY views DESC
            LIMIT " . (int)$limit
        );
        
        $labels = [];
        $data = [];
        $viewsTotal = 0;
        
        foreach ($posts as $post) {
            $labels[] = mb_strlen($post['title']) > 30 ? mb_substr($post['title'], 0, 27) . '...' : $post['title'];
            $data[] = (int)$post['views'];
            $viewsTotal += (int)$post['views'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'posts' => $posts,
            'total_views' => $viewsTotal
        ];
    }
    
    /**
    * Топ залайканных постов
    */
    private function getLikedPostsStats() {
        $limit = (int)($_GET['limit'] ?? 5);
        $limit = max(1, min(20, $limit));
        
        $posts = $this->db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.likes_count, p.featured_image, p.created_at,
                    COUNT(DISTINCT pl.user_id) as actual_likes
            FROM posts p
            LEFT JOIN post_likes pl ON p.id = pl.post_id
            WHERE p.status = 'published'
            GROUP BY p.id
            ORDER BY actual_likes DESC, p.likes_count DESC
            LIMIT " . (int)$limit
        );
        
        $labels = [];
        $data = [];
        $likesTotal = 0;
        
        foreach ($posts as $post) {
            $likes = (int)($post['actual_likes'] ?: $post['likes_count']);
            $labels[] = mb_strlen($post['title']) > 30 ? mb_substr($post['title'], 0, 27) . '...' : $post['title'];
            $data[] = $likes;
            $likesTotal += $likes;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'posts' => $posts,
            'total_likes' => $likesTotal
        ];
    }
    
    /**
    * Статистика комментариев
    */
    private function getCommentsStats($period) {
        $labels = [];
        $data = [];
        
        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d.m', strtotime($date));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM comments 
                        WHERE DATE(created_at) = ? AND status = 'approved'",
                        [$date]
                    );
                    $data[] = (int)$count;
                }
                break;
                
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d.m', strtotime($date));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM comments 
                        WHERE DATE(created_at) = ? AND status = 'approved'",
                        [$date]
                    );
                    $data[] = (int)$count;
                }
                break;
                
            default:
                for ($i = 11; $i >= 0; $i--) {
                    $month = date('Y-m', strtotime("-$i months"));
                    $labels[] = date('M Y', strtotime($month));
                    
                    $count = $this->db->fetchValue(
                        "SELECT COUNT(*) FROM comments 
                        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'approved'",
                        [$month]
                    );
                    $data[] = (int)$count;
                }
                break;
        }
        
        $statusStats = $this->db->fetch(
            "SELECT 
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'spam' THEN 1 ELSE 0 END) as spam
            FROM comments"
        );
        
        $topPosts = $this->db->fetchAll(
            "SELECT p.id, p.title, p.slug, COUNT(c.id) as comments_count
            FROM posts p
            LEFT JOIN comments c ON p.id = c.post_id AND c.status = 'approved'
            WHERE p.status = 'published'
            GROUP BY p.id
            ORDER BY comments_count DESC
            LIMIT 5"
        );
        
        return [
            'labels' => $labels,
            'data' => $data,
            'status' => [
                'approved' => (int)($statusStats['approved'] ?? 0),
                'pending' => (int)($statusStats['pending'] ?? 0),
                'spam' => (int)($statusStats['spam'] ?? 0)
            ],
            'top_posts' => $topPosts
        ];
    }
    
    /**
    * Сводная статистика
    */
    private function getSummaryStats() {
        $categoriesStats = $this->db->fetchAll(
            "SELECT c.name, COUNT(p.id) as posts_count
            FROM categories c
            LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
            GROUP BY c.id
            ORDER BY posts_count DESC
            LIMIT 10"
        );
        
        $monthlyStats = $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM posts 
            WHERE status = 'published'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC"
        );
        
        $userActivity = $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT user_id) as active_commenters
            FROM comments 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND user_id IS NOT NULL"
        );
        
        return [
            'categories' => $categoriesStats,
            'monthly' => $monthlyStats,
            'active_commenters' => (int)($userActivity['active_commenters'] ?? 0)
        ];
    }

    /**
    * Экспорт в HTML отчет
    */
    public function exportHtmlAction() {
        
        $period = $_GET['period'] ?? 'month';
        $type = $_GET['type'] ?? 'full';
        
        $data = [];
        if ($type === 'full') {
            $data = $this->getFullReportData($period);
        } elseif ($type === 'publications') {
            $data = $this->getPublicationsReportData($period);
        } elseif ($type === 'popular') {
            $data = $this->getPopularReportData();
        } elseif ($type === 'comments') {
            $data = $this->getCommentsReportData($period);
        }
        
        $html = $this->renderHtmlReport($data, $type, $period);
        
        $filename = 'statistics-report-' . date('Y-m-d-H-i') . '.html';
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        
        echo $html;
        exit;
    }
    
    /**
    * Получение данных для полного отчета
    */
    private function getFullReportData($period) {
        switch ($period) {
            case 'week': $days = 7; break;
            case 'quarter': $days = 90; break;
            case 'year': $days = 365; break;
            default: $days = 30;
        }
        
        $dailyData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateLabel = date('d.m.Y', strtotime($date));
            
            $postsCount = 0;
            $postsResult = $this->db->fetch(
                "SELECT COUNT(*) as cnt FROM posts WHERE DATE(created_at) = ? AND status = 'published'",
                [$date]
            );
            if ($postsResult) $postsCount = (int)$postsResult['cnt'];
            
            $commentsCount = 0;
            $commentsResult = $this->db->fetch(
                "SELECT COUNT(*) as cnt FROM comments WHERE DATE(created_at) = ? AND status = 'approved'",
                [$date]
            );
            if ($commentsResult) $commentsCount = (int)$commentsResult['cnt'];
            
            $viewsCount = 0;
            $viewsResult = $this->db->fetch(
                "SELECT COALESCE(SUM(views), 0) as total FROM posts WHERE DATE(created_at) = ? AND status = 'published'",
                [$date]
            );
            if ($viewsResult) $viewsCount = (int)$viewsResult['total'];
            
            $likesCount = 0;
            $likesResult = $this->db->fetch(
                "SELECT COUNT(*) as cnt FROM post_likes WHERE DATE(created_at) = ?",
                [$date]
            );
            if ($likesResult) $likesCount = (int)$likesResult['cnt'];
            
            $dailyData[] = [
                'date' => $dateLabel,
                'posts' => $postsCount,
                'comments' => $commentsCount,
                'views' => $viewsCount,
                'likes' => $likesCount
            ];
        }
        
        $totalPosts = 0;
        $totalResult = $this->db->fetch("SELECT COUNT(*) as cnt FROM posts WHERE status = 'published'");
        if ($totalResult) $totalPosts = (int)$totalResult['cnt'];
        
        $totalComments = 0;
        $commentsTotalResult = $this->db->fetch("SELECT COUNT(*) as cnt FROM comments WHERE status = 'approved'");
        if ($commentsTotalResult) $totalComments = (int)$commentsTotalResult['cnt'];
        
        $totalViews = 0;
        $viewsTotalResult = $this->db->fetch("SELECT COALESCE(SUM(views), 0) as total FROM posts WHERE status = 'published'");
        if ($viewsTotalResult) $totalViews = (int)$viewsTotalResult['total'];
        
        $totalLikes = 0;
        $likesTotalResult = $this->db->fetch("SELECT COUNT(*) as cnt FROM post_likes");
        if ($likesTotalResult) $totalLikes = (int)$likesTotalResult['cnt'];
        
        $categories = $this->db->fetchAll(
            "SELECT c.name, COUNT(p.id) as posts_count
            FROM categories c
            LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
            GROUP BY c.id
            ORDER BY posts_count DESC"
        );
        
        $topPosts = $this->db->fetchAll(
            "SELECT p.title, p.views, p.likes_count, 
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comments_count
            FROM posts p
            WHERE p.status = 'published'
            ORDER BY p.views DESC
            LIMIT 10"
        );
        
        return [
            'daily' => $dailyData,
            'total_posts' => $totalPosts,
            'total_comments' => $totalComments,
            'total_views' => $totalViews,
            'total_likes' => $totalLikes,
            'categories' => $categories,
            'top_posts' => $topPosts
        ];
    }
    
    /**
    * Рендеринг HTML отчета
    */
    private function renderHtmlReport($data, $type, $period) {
        $siteName = \SettingsHelper::get('general', 'site_name', 'BloggyCMS');
        $reportDate = date('d.m.Y H:i:s');
        
        $typeLabels = [
            'full' => 'Полный отчет',
            'publications' => 'Публикации',
            'popular' => 'Популярные посты',
            'comments' => 'Комментарии'
        ];
        
        $periodLabels = [
            'week' => 'Последние 7 дней',
            'month' => 'Последние 30 дней',
            'quarter' => 'Последние 90 дней',
            'year' => 'Последние 12 месяцев',
            'all' => 'За все время'
        ];
        
        $periodLabel = $this->getPeriodLabel($period);
        $typeLabel = $this->getReportTypeLabel($type);
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Статистический отчет - <?php echo htmlspecialchars($siteName); ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #f0f2f5;
                    padding: 20px;
                }
                .report-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .report-header {
                    background: linear-gradient(135deg, #263e6b 0%, #0d2a6f 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .report-header h1 { font-size: 28px; margin-bottom: 8px; }
                .report-header .subtitle { opacity: 0.9; font-size: 13px; }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 15px;
                    padding: 25px;
                    background: #f8f9fa;
                }
                .stat-card {
                    background: white;
                    padding: 15px;
                    border-radius: 10px;
                    text-align: center;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .stat-card .stat-value { font-size: 28px; font-weight: bold; color: #667eea; }
                .stat-card .stat-label { color: #6c757d; font-size: 12px; margin-top: 5px; }
                .section { padding: 25px; border-bottom: 1px solid #e9ecef; }
                .section-title {
                    font-size: 20px;
                    margin-bottom: 15px;
                    color: #333;
                    border-left: 3px solid #667eea;
                    padding-left: 12px;
                }
                .data-table { width: 100%; border-collapse: collapse; }
                .data-table th { background: #f8f9fa; padding: 10px; text-align: left; font-weight: 600; border-bottom: 2px solid #dee2e6; }
                .data-table td { padding: 8px 10px; border-bottom: 1px solid #e9ecef; }
                .data-table tr:hover { background: #f8f9fa; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; color: #6c757d; font-size: 11px; }
                @media print {
                    body { background: white; padding: 0; }
                    .report-container { box-shadow: none; }
                    .stats-grid { background: white; }
                }
            </style>
        </head>
        <body>
            <div class="report-container">
                <div class="report-header">
                    <h1>📊 Статистический отчет</h1>
                    <div class="subtitle"><?php echo htmlspecialchars($siteName); ?> • <?php echo $reportDate; ?></div>
                    <div class="subtitle">Тип отчета: <?php echo $typeLabel; ?> • Период: <?php echo $periodLabel; ?></div>
                </div>
                
                <?php if ($type === 'full' && isset($data['total_posts'])): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($data['total_posts'], 0, ',', ' '); ?></div>
                        <div class="stat-label">📝 Всего постов</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($data['total_comments'] ?? 0, 0, ',', ' '); ?></div>
                        <div class="stat-label">💬 Всего комментариев</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($data['total_views'] ?? 0, 0, ',', ' '); ?></div>
                        <div class="stat-label">👁️ Всего просмотров</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($data['total_likes'] ?? 0, 0, ',', ' '); ?></div>
                        <div class="stat-label">❤️ Всего лайков</div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($data['daily'])): ?>
                <div class="section">
                    <h2 class="section-title">📈 Ежедневная статистика</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <?php if ($type === 'full' || $type === 'publications'): ?><th>Посты</th><?php endif; ?>
                                <?php if ($type === 'full' || $type === 'comments'): ?><th>Комментарии</th><?php endif; ?>
                                <?php if ($type === 'full'): ?><th>Просмотры</th><?php endif; ?>
                                <?php if ($type === 'full'): ?><th>Лайки</th><?php endif; ?>
                                <?php if ($type === 'publications'): ?><th>Накоплено</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['daily'] as $row): ?>
                            <tr>
                                <td><?php echo $row['date']; ?></td>
                                <?php if ($type === 'full' || $type === 'publications'): ?>
                                <td><?php echo $row['posts'] ?? $row['count'] ?? 0; ?></td>
                                <?php endif; ?>
                                <?php if ($type === 'full' || $type === 'comments'): ?>
                                <td><?php echo $row['comments'] ?? $row['total'] ?? 0; ?></td>
                                <?php endif; ?>
                                <?php if ($type === 'full'): ?>
                                <td><?php echo number_format($row['views'] ?? 0, 0, ',', ' '); ?></td>
                                <td><?php echo $row['likes'] ?? 0; ?></td>
                                <?php endif; ?>
                                <?php if ($type === 'publications' && isset($row['total'])): ?>
                                <td><strong><?php echo $row['total']; ?></strong></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <?php if ($type === 'full' && !empty($data['categories'])): ?>
                <div class="section">
                    <h2 class="section-title">🏷️ Статистика по категориям</h2>
                    <?php 
                    $maxPosts = max(array_column($data['categories'], 'posts_count'));
                    foreach ($data['categories'] as $cat):
                        $percent = $maxPosts > 0 ? round($cat['posts_count'] / $maxPosts * 100) : 0;
                    ?>
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            <span><strong><?php echo $cat['posts_count']; ?></strong> постов</span>
                        </div>
                        <div style="background: #e9ecef; border-radius: 4px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #667eea, #764ba2); width: <?php echo $percent; ?>%; height: 6px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if (($type === 'full' || $type === 'popular') && !empty($data['top_posts'] ?? $data['posts'] ?? null)): 
                    $topPosts = $data['top_posts'] ?? $data['posts'] ?? [];
                ?>
                <div class="section">
                    <h2 class="section-title">⭐ Топ постов</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Просмотры</th>
                                <?php if ($type === 'full'): ?><th>Лайки</th><th>Комментарии</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($topPosts as $post): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars(mb_substr($post['title'], 0, 50)); ?></td>
                                <td><?php echo number_format($post['views'] ?? 0, 0, ',', ' '); ?></td>
                                <?php if ($type === 'full'): ?>
                                <td><?php echo $post['likes_count'] ?? 0; ?></td>
                                <td><?php echo $post['comments_count'] ?? 0; ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <div class="footer">
                    <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?> • Отчет сгенерирован автоматически</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function getReportTypeLabel($type) {
        $labels = [
            'full' => 'Полный отчет',
            'publications' => 'Публикации',
            'popular' => 'Популярные посты',
            'comments' => 'Комментарии'
        ];
        return $labels[$type] ?? 'Полный отчет';
    }

    private function getPublicationsReportData($period) {
        $days = 30;
        if ($period === 'week') $days = 7;
        if ($period === 'quarter') $days = 90;
        
        $dailyData = [];
        $total = 0;
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = (int)$this->db->fetchValue(
                "SELECT COUNT(*) FROM posts WHERE DATE(created_at) = ? AND status = 'published'",
                [$date]
            );
            $total += $count;
            $dailyData[] = [
                'date' => date('d.m.Y', strtotime($date)),
                'count' => $count,
                'total' => $total
            ];
        }
        
        $totalPosts = (int)$this->db->fetchValue("SELECT COUNT(*) FROM posts WHERE status = 'published'");
        
        return [
            'daily' => $dailyData,
            'total' => $totalPosts
        ];
    }
    
    private function getPopularReportData() {
        $posts = $this->db->fetchAll(
            "SELECT title, views, likes_count, 
                    DATE_FORMAT(created_at, '%d.%m.%Y') as created_at
            FROM posts 
            WHERE status = 'published'
            ORDER BY views DESC
            LIMIT 50"
        );
        
        $totalViews = (int)$this->db->fetchValue("SELECT COALESCE(SUM(views), 0) FROM posts WHERE status = 'published'");
        
        return [
            'posts' => $posts,
            'total_views' => $totalViews,
            'total_posts' => count($posts)
        ];
    }
    
    private function getCommentsReportData($period) {
        $days = 30;
        if ($period === 'week') $days = 7;
        
        $dailyData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailyData[] = [
                'date' => date('d.m.Y', strtotime($date)),
                'total' => (int)$this->db->fetchValue(
                    "SELECT COUNT(*) FROM comments WHERE DATE(created_at) = ?",
                    [$date]
                ),
                'approved' => (int)$this->db->fetchValue(
                    "SELECT COUNT(*) FROM comments WHERE DATE(created_at) = ? AND status = 'approved'",
                    [$date]
                ),
                'pending' => (int)$this->db->fetchValue(
                    "SELECT COUNT(*) FROM comments WHERE DATE(created_at) = ? AND status = 'pending'",
                    [$date]
                ),
                'spam' => (int)$this->db->fetchValue(
                    "SELECT COUNT(*) FROM comments WHERE DATE(created_at) = ? AND status = 'spam'",
                    [$date]
                )
            ];
        }
        
        $totalComments = (int)$this->db->fetchValue("SELECT COUNT(*) FROM comments");
        $approvedComments = (int)$this->db->fetchValue("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
        $pendingComments = (int)$this->db->fetchValue("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
        $spamComments = (int)$this->db->fetchValue("SELECT COUNT(*) FROM comments WHERE status = 'spam'");
        
        $topPosts = $this->db->fetchAll(
            "SELECT p.id, p.title, p.slug, COUNT(c.id) as comments_count
            FROM posts p
            LEFT JOIN comments c ON p.id = c.post_id AND c.status = 'approved'
            WHERE p.status = 'published'
            GROUP BY p.id
            ORDER BY comments_count DESC
            LIMIT 5"
        );
        
        return [
            'daily' => $dailyData,
            'total_comments' => $totalComments,
            'approved_comments' => $approvedComments,
            'pending_comments' => $pendingComments,
            'spam_comments' => $spamComments,
            'top_posts' => $topPosts
        ];
    }

    /**
    * Получает название периода
    */
    private function getPeriodLabel($period) {
        $labels = [
            'week' => 'Последние 7 дней',
            'month' => 'Последние 30 дней',
            'quarter' => 'Последние 90 дней',
            'year' => 'Последние 12 месяцев',
            'all' => 'За все время'
        ];
        return $labels[$period] ?? 'Последние 30 дней';
    }
    
}