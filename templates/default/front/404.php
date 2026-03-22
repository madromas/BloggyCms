<?php
/**
 * Template Name: Страница не найдена
 */
?>

<div class="tg-error-404">
    <div class="tg-container">
        <div class="tg-error-content">
            <div class="tg-error-code">404</div>
            <h1 class="tg-error-title">Страница не найдена</h1>
            <p class="tg-error-description">
                К сожалению, запрашиваемая страница не существует или была удалена.
            </p>
            <div class="tg-error-actions">
                <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">
                    <?php echo bloggy_icon('bs', 'house', '16', 'currentColor', 'me-1'); ?>
                    Вернуться на главную
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <?php echo bloggy_icon('bs', 'arrow-left', '16', 'currentColor', 'me-1'); ?>
                    Вернуться назад
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.tg-error-404 {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 0;
}
.tg-error-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}
.tg-error-code {
    font-size: 120px;
    font-weight: 700;
    color: var(--tg-primary, #2d5f94);
    line-height: 1;
    margin-bottom: 1rem;
}
.tg-error-title {
    font-size: 24px;
    margin-bottom: 1rem;
}
.tg-error-description {
    color: var(--tg-text-secondary, #666);
    margin-bottom: 2rem;
}
.tg-error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}
@media (max-width: 768px) {
    .tg-error-code {
        font-size: 80px;
    }
    .tg-error-title {
        font-size: 20px;
    }
    .tg-error-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>