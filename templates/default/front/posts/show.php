<?php
/**
 * Template Name: Страница поста
 */

$isPasswordProtected = $post['password_protected'] == 1; 
$allowComments = isset($post['allow_comments']) ? $post['allow_comments'] == 1 : true;
$templatePath = TEMPLATES_PATH . '/' . DEFAULT_TEMPLATE;
$totalComments = $totalComments ?? ($post['comments_count'] ?? 0);
?>
<style>
/* === Общие стили для страницы поста === */
.tg-post-page {
    background-color: #f9fafb;
    padding: 2rem 0;
    min-height: 100vh;
}

.tg-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* === Заголовок поста === */
.tg-post-header {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.tg-post-meta-top {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #6b7280;
}

.tg-post-category {
    background-color: #e0e7ff;
    color: #4f46e5;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.tg-post-category:hover {
    background-color: #c7d2fe;
}

.tg-post-date,
.tg-post-protected {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.tg-post-protected span {
    color: #ef4444;
    font-weight: 500;
}

.tg-post-title {
    font-size: 2.2rem;
    font-weight: 700;
    line-height: 1.3;
    margin: 0.8rem 0 1.5rem;
    color: #111827;
}

.tg-post-author {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.tg-author-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.tg-avatar-placeholder-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #6366f1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.tg-author-info {
    font-size: 0.95rem;
    color: #4b5563;
}

.tg-author-name {
    font-weight: 600;
    color: #1f2937;
}

/* === Панель действий === */
.tg-post-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.tg-action-btn {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.tg-action-btn:hover {
    background-color: #f3f4f6;
    color: #4f46e5;
}

.tg-action-btn.tg-active {
    color: #ec4899;
}

.tg-action-count {
    font-weight: 600;
}

.tg-post-views {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: #6b7280;
    font-size: 0.9rem;
}

/* === Изображение поста === */
.tg-post-image-full {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

.tg-post-image-full img {
    width: 100%;
    height: auto;
    display: block;
}

/* === Контент поста === */
.tg-post-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    line-height: 1.7;
    color: #374151;
}

.tg-post-block {
    margin-bottom: 1.5rem;
}

/* === Теги === */
.tg-post-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
}

.tg-tag {
    background-color: #e5e7eb;
    color: #4b5563;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    transition: background-color 0.2s ease;
}

.tg-tag:hover {
    background-color: #d1d5db;
}

/* === Секция комментариев === */
.tg-comments-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.tg-comments-header {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
}

.tg-comments-title {
    font-size: 1.4rem;
    color: #111827;
    margin: 0;
}

.tg-comments-count {
    background-color: #e0e7ff;
    color: #4f46e5;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.tg-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background: white;
    color: #4b5563;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.9rem;
}

.tg-btn:hover {
    background-color: #f3f4f6;
}

.tg-btn-outline {
    background: transparent;
}

/* === Уведомления === */
.tg-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.tg-alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.tg-alert-info {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

.tg-alert-icon {
    margin-top: 0.1rem;
}

.tg-alert-content strong {
    display: block;
    margin-bottom: 0.2rem;
.tg-alert-link {
    color: #2563eb;
    text-decoration: underline;
    font-weight: 500;
    margin-top: 0.3rem;
    display: inline-block;
}

/* === Отступы === */
.tg-mb-4 {
    margin-bottom: 1.5rem;
}

.tg-mt-5 {
    margin-top: 2rem;
}

.tg-ml-auto {
    margin-left: auto;
}

.tg-mr-1 {
    margin-right: 0.25rem;
}

.tg-mb-0 {
    margin-bottom: 0 !important;
}

.tg-mb-5 {
    margin-bottom: 2.5rem;
}

/* === Адаптивность === */
@media (max-width: 768px) {
    .tg-container {
        padding: 0 1rem;
    }

    .tg-post-title {
        font-size: 1.8rem;
    }

    .tg-post-header,
    .tg-post-content,
    .tg-comments-section {
        padding: 1.5rem;
    }

    .tg-post-meta-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .tg-post-actions {
        flex-wrap: wrap;
    }
}
</style>
<div class="tg-post-page">
    <div class="tg-container">
        
        <div class="tg-post-header tg-mb-4">
            <div class="tg-post-meta-top">
                <?php if (!empty($post['category_name'])) { ?>
                <a href="<?php echo BASE_URL; ?>/category/<?php echo html($post['category_slug']); ?>" 
                   class="tg-post-category">
                    <?php echo html($post['category_name']); ?>
                </a>
                <?php } ?>
                
                <span class="tg-post-date">
                    <?php echo bloggy_icon('bs', 'calendar', '14', 'currentColor', 'tg-mr-1'); ?>
                    <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                </span>
                
                <?php if ($isPasswordProtected) { ?>
                <span class="tg-post-protected" title="Защищено паролем">
                    <?php echo bloggy_icon('bs', 'lock-fill', '14', 'currentColor'); ?>
                    <span>Защищено</span>
                </span>
                <?php } ?>
            </div>
            
            <h1 class="tg-post-title">
                <?php echo html($post['title']); ?>
            </h1>
            
            <div class="tg-post-author">
                <div class="tg-author-avatar">
                    <?php if (!empty($post['author_avatar']) && $post['author_avatar'] !== 'default.jpg') { ?>
                        <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo html($post['author_avatar']); ?>" 
                             alt="<?php echo html($post['author_name'] ?? 'Автор'); ?>">
                    <?php } else { ?>
                        <div class="tg-avatar-placeholder-small">
                            <?php echo strtoupper(substr(($post['author_name'] ?? 'A'), 0, 1)); ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="tg-author-info">
                    <span class="tg-author-name"><?php echo html($post['author_name'] ?? 'Автор'); ?></span>
                </div>
            </div>
            
            <div class="tg-post-actions tg-mt-3">
                <button class="tg-action-btn tg-like-btn <?php echo isset($userLiked) && $userLiked ? 'tg-active' : ''; ?>" 
                        data-post-id="<?php echo $post['id']; ?>"
                        title="Нравится">
                    <?php 
                    $heartIcon = (isset($userLiked) && $userLiked) ? 'heart-fill' : 'heart';
                    echo bloggy_icon('bs', $heartIcon, '18', 'currentColor', 'tg-mr-1');
                    ?>
                    <span class="tg-action-count"><?php echo $post['likes_count'] ?? 0; ?></span>
                </button>
                
                <button class="tg-action-btn tg-bookmark-btn <?php echo isset($userBookmarked) && $userBookmarked ? 'tg-active' : ''; ?>" 
                        data-post-id="<?php echo $post['id']; ?>"
                        title="В закладки">
                    <?php 
                    $bookmarkIcon = (isset($userBookmarked) && $userBookmarked) ? 'bookmark-fill' : 'bookmark';
                    echo bloggy_icon('bs', $bookmarkIcon, '18', 'currentColor', 'tg-mr-1');
                    ?>
                    <span>В закладки</span>
                </button>
                
                <a href="#comments" class="tg-action-btn">
                    <?php echo bloggy_icon('bs', 'chat-dots', '18', 'currentColor', 'tg-mr-1'); ?>
                    <span class="tg-action-count"><?php echo $post['comments_count'] ?? 0; ?></span>
                </a>
                
                <div class="tg-post-views tg-ml-auto">
                    <?php echo bloggy_icon('bs', 'eye', '16', 'currentColor', 'tg-mr-1'); ?>
                    <span><?php echo $post['views'] ?? 0; ?></span>
                </div>
            </div>
        </div>
        
        <?php if ($post['featured_image']) { ?>
        <div class="tg-post-image-full tg-mb-4">
            <img src="<?php echo BASE_URL; ?>/uploads/images/<?php echo html($post['featured_image']); ?>" 
                 alt="<?php echo html($post['title']); ?>"
                 loading="lazy">
        </div>
        <?php } ?>
        
        <div class="tg-post-content tg-mb-5">
            <?php if (!empty($blocks)) { ?>
                <?php foreach ($blocks as $block) { ?>
                    <div class="tg-post-block tg-post-block-<?php echo $block['type']; ?> tg-mb-4">
                        <?php 
                        if (is_array($block['content'])) {
                            echo BlockRenderer::render($block);
                        } else {
                            echo $block['content'];
                        }
                        ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        
        <?php if (!empty($post['tags'])) { ?>
        <div class="tg-post-tags tg-mb-4">
            <?php foreach ($post['tags'] as $tag) { ?>
            <a href="<?php echo BASE_URL; ?>/tag/<?php echo html($tag['slug']); ?>" class="tg-tag">
                #<?php echo html($tag['name']); ?>
            </a>
            <?php } ?>
        </div>
        <?php } ?>
        
        <?php if (!$isPasswordProtected && $allowComments) { ?>
        <section id="comments" class="tg-comments-section tg-mt-5">
            <?php if (isset($_GET['comment_updated'])) { ?>
            <div class="tg-alert tg-alert-success tg-mb-4">
                <div class="tg-alert-icon">
                    <?php echo bloggy_icon('bs', 'check-circle', '18', 'currentColor'); ?>
                </div>
                <div class="tg-alert-content">
                    <strong>Комментарий отправлен!</strong>
                    <p class="tg-mb-0">Он появится после проверки модератором.</p>
                    <?php if (isset($_GET['scroll_to_comment'])) { ?>
                    <a href="#comment-<?php echo (int)$_GET['scroll_to_comment']; ?>" class="tg-alert-link">
                        Перейти к комментарию
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            
            <div class="tg-comments-header">
                <h3 class="tg-comments-title">
                    <?php echo bloggy_icon('bs', 'chat-dots', '20', 'currentColor', 'tg-mr-1'); ?>
                    Комментарии
                </h3>
                <span class="tg-comments-count"><?php echo $totalComments; ?></span>
                <button class="tg-btn tg-btn-sm tg-btn-outline tg-ml-auto" onclick="scrollToCommentForm()">
                    <?php echo bloggy_icon('bs', 'pen', '14', 'currentColor', 'tg-mr-1'); ?>
                    Написать
                </button>
            </div>
            
            <?php include $templatePath . '/front/comments/list.php'; ?>
            
            <?php include $templatePath . '/front/comments/form.php'; ?>
            
        </section>
        <?php } elseif ($isPasswordProtected) { ?>
        <div class="tg-comments-disabled tg-mt-5">
            <div class="tg-alert tg-alert-info">
                <div class="tg-alert-icon">
                    <?php echo bloggy_icon('bs', 'lock', '18', 'currentColor'); ?>
                </div>
                <div class="tg-alert-content">
                    <strong>Комментирование недоступно</strong>
                    <p class="tg-mb-0">Эта запись защищена паролем.</p>
                </div>
            </div>
        </div>
        <?php } elseif (!$allowComments) { ?>
        <div class="tg-comments-disabled tg-mt-5">
            <div class="tg-alert tg-alert-info">
                <div class="tg-alert-icon">
                    <?php echo bloggy_icon('bs', 'chat-left-x', '18', 'currentColor'); ?>
                </div>
                <div class="tg-alert-content">
                    <strong>Комментирование отключено</strong>
                    <p class="tg-mb-0">Автор отключил возможность комментирования.</p>
                </div>
            </div>
        </div>
        <?php } ?>
        
    </div>
</div>

<?php 
ob_start();
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.querySelector('.tg-like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                window.location.href = '<?php echo BASE_URL; ?>/login';
                return;
            }
            
            const postId = this.dataset.postId;
            const countSpan = this.querySelector('.tg-action-count');
            const icon = this.querySelector('svg use');
            const wasActive = this.classList.contains('tg-active');
            this.classList.toggle('tg-active');
            
            if (countSpan) {
                const currentCount = parseInt(countSpan.textContent) || 0;
                countSpan.textContent = wasActive ? currentCount - 1 : currentCount + 1;
            }
            
            if (icon) {
                const iconHref = icon.getAttribute('href');
                const newIcon = wasActive ? 'heart' : 'heart-fill';
                icon.setAttribute('href', iconHref.replace(/heart(-fill)?/, newIcon));
            }
            
            fetch(`<?php echo BASE_URL; ?>/post/like/${postId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(() => {
                this.classList.toggle('tg-active');
                if (countSpan) {
                    const currentCount = parseInt(countSpan.textContent) || 0;
                    countSpan.textContent = wasActive ? currentCount + 1 : currentCount - 1;
                }
            });
        });
    }
    
    const bookmarkBtn = document.querySelector('.tg-bookmark-btn');
    if (bookmarkBtn) {
        bookmarkBtn.addEventListener('click', function() {
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                window.location.href = '<?php echo BASE_URL; ?>/login';
                return;
            }
            
            const postId = this.dataset.postId;
            const icon = this.querySelector('svg use');
            const wasActive = this.classList.contains('tg-active');
            
            this.classList.toggle('tg-active');
            
            if (icon) {
                const iconHref = icon.getAttribute('href');
                const newIcon = wasActive ? 'bookmark' : 'bookmark-fill';
                icon.setAttribute('href', iconHref.replace(/bookmark(-fill)?/, newIcon));
            }
            
            fetch(`<?php echo BASE_URL; ?>/post/bookmark/${postId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(() => {
                this.classList.toggle('tg-active');
            });
        });
    }
});

function scrollToCommentForm() {
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => {
            const textarea = commentForm.querySelector('textarea');
            if (textarea) textarea.focus();
        }, 300);
    }
}
</script>
<?php front_bottom_js(ob_get_clean()); ?>
<?php echo add_frontend_js('/templates/default/front/assets/js/user-action.js'); ?>