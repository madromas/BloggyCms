<?php
/**
 * Template Name: Страница поста, защищенного паролем
 */
?>
<style>
/* === Общие стили для страницы ввода пароля === */
.tg-password-page {
    background: linear-gradient(135deg, #f0f4f8, #e6e9f0);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

.tg-container-sm {
    max-width: 500px;
    width: 100%;
    margin: 0 auto;
}

/* === Карточка формы === */
.tg-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.tg-card-body {
    padding: 2.5rem;
}

/* === Иконка замка === */
.tg-password-icon {
    margin-bottom: 1.5rem;
}

.tg-password-icon svg {
    color: #4f46e5;
}

/* === Заголовки === */
.tg-password-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.75rem;
}

.tg-password-post-title {
    font-size: 1.1rem;
    color: #6b7280;
}

.tg-post-title-block {
    display: block;
    font-size: 1.25rem;
    color: #1f2937;
    margin-top: 0.5rem;
    word-break: break-word;
}

/* === Описание === */
.tg-password-description {
    font-size: 1rem;
    color: #6b7280;
    line-height: 1.6;
}

/* === Уведомление об ошибке === */
.tg-alert-error {
    background-color: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.tg-alert-icon {
    margin-top: 0.1rem;
}

.tg-alert-content strong {
    display: block;
    margin-bottom: 0.2rem;
}

/* === Форма пароля === */
.tg-password-form {
    margin-top: 1rem;
}

.tg-field {
    margin-bottom: 1.5rem;
}

.tg-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.tg-input-wrapper {
    position: relative;
}

.tg-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.tg-input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.tg-input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.tg-field-hint {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* === Кнопки и действия === */
.tg-password-actions {
    margin-top: 1.5rem;
}

.tg-btn-block {
    width: 100%;
}

.tg-btn-primary {
    background-color: #4f46e5;
    color: white;
    border: none;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0. transition: background-color 0.2s ease;
}

.tg-btn-primary:hover {
    background-color: #4338ca;
}

.tg-password-back-link {
    display: inline-block;
    margin-top: 1rem;
    color: #4f46e5;
    text-align: center;
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.tg-password-back-link:hover {
    text-decoration: underline;
}

/* === Текст помощи внизу === */
.tg-password-help {
    margin-top: 1.5rem;
    color: #6b7280;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
}

/* === Центрирование текста === */
.tg-text-center {
    text-align: center;
}

.tg-text-muted {
    color: #6b7280;
}

.tg-mb-3 {
    margin-bottom: 1.25rem;
}

.tg-mb-2 {
    margin-bottom: 0.75rem;
}

.tg-mb-4 {
    margin-bottom: 1.5rem;
}

.tg-mt-3 {
    margin-top: 1rem;
}

.tg-mt-4 {
    margin-top: 1.5rem;
}

.tg-mb-0 {
    margin-bottom: 0 !important;
}

.tg-mr-1 {
    margin-right: 0.25rem;
}

/* === Адаптивность === */
@media (max-width: 768px) {
    .tg-card-body {
        padding: 1.75rem;
    }

    .tg-password-title {
        font-size: 1.5rem;
    }

    .tg-password-post-title {
        font-size: 1rem;
    }

    .tg-post-title-block {
        font-size: 1.1rem;
    }
}
</style>
<div class="tg-password-page">
    <div class="tg-container tg-container-sm">
        
        <div class="tg-card">
            <div class="tg-card-body">
                
                <div class="tg-password-icon tg-text-center tg-mb-3">
                    <?php echo bloggy_icon('bs', 'lock-fill', '48', 'var(--tg-primary)'); ?>
                </div>
                
                <h1 class="tg-password-title tg-text-center tg-mb-2">
                    Защищенный пост
                </h1>
                
                <div class="tg-password-post-title tg-text-center tg-mb-4">
                    <span class="tg-text-muted">Доступ ограничен для:</span>
                    <strong class="tg-post-title-block"><?php echo html($post['title']); ?></strong>
                </div>
                
                <?php if (!empty($post['short_description'])) { ?>
                <p class="tg-password-description tg-text-center tg-text-muted tg-mb-4">
                    <?php echo html($post['short_description']); ?>
                </p>
                <?php } ?>
                
                <?php if ($error) { ?>
                <div class="tg-alert tg-alert-error tg-mb-4">
                    <div class="tg-alert-icon">
                        <?php echo bloggy_icon('bs', 'exclamation-triangle', '18', 'currentColor'); ?>
                    </div>
                    <div class="tg-alert-content">
                        <strong>Неверный пароль</strong>
                        <p class="tg-mb-0">Пожалуйста, попробуйте еще раз.</p>
                    </div>
                </div>
                <?php } ?>
                
                <form method="post" action="<?php echo BASE_URL; ?>/post/check-password/<?php echo $post['id']; ?>" class="tg-password-form">
                    <input type="hidden" name="redirect" value="<?php echo BASE_URL; ?>/post/<?php echo html($post['slug']); ?>">
                    
                    <div class="tg-field tg-mb-4">
                        <label for="password" class="tg-label">Пароль доступа</label>
                        <div class="tg-input-wrapper">
                            <span class="tg-input-icon">
                                <?php echo bloggy_icon('bs', 'key', '16', 'currentColor'); ?>
                            </span>
                            <input type="password" 
                                   id="password"
                                   name="password" 
                                   class="tg-input" 
                                   placeholder="Введите пароль..." 
                                   required
                                   autofocus>
                        </div>
                        <div class="tg-field-hint">
                            <?php echo bloggy_icon('bs', 'info-circle', '12', 'currentColor', 'tg-mr-1'); ?>
                            Введите пароль для доступа к этому посту
                        </div>
                    </div>
                    
                    <div class="tg-password-actions">
                        <button type="submit" class="tg-btn tg-btn-primary tg-btn-block">
                            <?php echo bloggy_icon('bs', 'unlock', '16', 'currentColor', 'tg-mr-1'); ?>
                            Открыть пост
                        </button>
                        
                        <a href="<?php echo BASE_URL; ?>/posts" class="tg-password-back-link tg-text-center tg-mt-3">
                            <?php echo bloggy_icon('bs', 'arrow-left', '14', 'currentColor', 'tg-mr-1'); ?>
                            Вернуться к списку постов
                        </a>
                    </div>
                </form>
                
            </div>
        </div>
        
        <div class="tg-password-help tg-text-center tg-mt-4">
            <small class="tg-text-muted">
                <?php echo bloggy_icon('bs', 'shield-check', '12', 'currentColor', 'tg-mr-1'); ?>
                Этот пост защищен паролем автором блога
            </small>
        </div>
        
    </div>
</div>