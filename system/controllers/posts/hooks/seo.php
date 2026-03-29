<?php
/**
* SEO хуки для контроллера постов
* Автоматически подключается при загрузке контроллера
*/

// Добавление мета-тегов для поста
\Event::listen('controller.render.before', function($data) {
    if (!isset($data['template']) || strpos($data['template'], 'posts/show') === false) {
        return;
    }
    
    if (!isset($data['post'])) {
        return;
    }
    
    $post = $data['post'];
    $seoModel = new SeoModel($GLOBALS['db']);
    $metaSettings = $seoModel->getMetaSettings();
    
    // Canonical URL
    if ($metaSettings['canonical_enabled']) {
        $canonical = BASE_URL . '/post/' . $post['slug'];
        \Event::trigger('seo.meta.canonical', ['url' => &$canonical]);
        $data['canonical_url'] = $canonical;
    }
    
    // Open Graph
    if ($metaSettings['og_enabled']) {
        $data['og_data'] = [
            'title' => $post['title'],
            'description' => $post['short_description'] ?? '',
            'url' => BASE_URL . '/post/' . $post['slug'],
            'type' => 'article',
            'image' => !empty($post['featured_image']) 
                ? BASE_URL . '/uploads/images/' . $post['featured_image'] 
                : ''
        ];
    }
    
    // Schema.org
    if ($metaSettings['schema_enabled']) {
        $data['schema_data'] = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post['title'],
            'datePublished' => $post['created_at'],
            'dateModified' => $post['updated_at'] ?? $post['created_at'],
            'author' => [
                '@type' => 'Person',
                'name' => $post['author_name'] ?? ''
            ]
        ];
    }
}, 100);