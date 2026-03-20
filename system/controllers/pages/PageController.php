<?php

/**
 * Контроллер для отображения страниц на фронтенде
 * 
 * @package Controllers
 * @extends Controller
 */
class PageController extends Controller {
    
    /** @var PageModel Модель для работы со страницами */
    private $pageModel;
    
    /** @var PostBlockModel Модель для работы с блоками контента */
    private $postBlockModel;
    
    /** @var PostBlockManager Менеджер для обработки блоков и управления ассетами */
    private $postBlockManager;
    
    /**
     * Конструктор контроллера
     * Инициализирует модели и менеджер для работы со страницами и блоками
     * 
     * @param object $db Подключение к базе данных
     */
    public function __construct($db) {
        parent::__construct($db);
        $this->pageModel = new PageModel($db);
        $this->postBlockModel = new PostBlockModel($db);
        $this->postBlockManager = new PostBlockManager($db);
    }
    
    /**
     * Отображает страницу по её URL-адресу (slug)
     * Загружает страницу, её блоки и пользовательские поля, подготавливает контент для отображения
     * 
     * @param string|null $slug URL-адрес страницы
     * @return void
     */
    public function showAction($slug = null) {
        $action = new \pages\actions\Show($this->db, ['slug' => $slug]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
     * Подготавливает блоки страницы для отображения
     * Загружает ассеты, обрабатывает контент и объединяет настройки
     * 
     * @param int $pageId ID страницы
     * @return array Массив обработанных блоков
     */
    private function preparePageBlocks($pageId) {

        $blocks = $this->postBlockModel->getByPage($pageId);
        
        $this->loadBlockAssets($blocks);
        
        $processedBlocks = [];
        foreach ($blocks as $block) {
            $processedBlocks[] = $this->processSingleBlock($block);
        }
        
        return $processedBlocks;
    }
    
    /**
     * Загружает фронтенд-ассеты (CSS, JS) для всех блоков на странице
     * 
     * @param array $blocks Массив блоков страницы
     * @return void
     */
    private function loadBlockAssets($blocks) {
        $blocksData = [];
        foreach ($blocks as $block) {
            $blocksData[] = [
                'type' => $block['type']
            ];
        }
        
        $this->postBlockManager->loadFrontendAssetsForBlocks($blocksData);
    }
    
    /**
     * Обрабатывает отдельный блок страницы
     * Декодирует JSON-данные, объединяет настройки и обрабатывает контент
     * 
     * @param array $block Данные блока из базы данных
     * @return array Обработанный блок готовый к отображению
     */
    private function processSingleBlock($block) {
        $content = $this->decodeJsonIfNeeded($block['content']);
        $settings = $this->decodeJsonIfNeeded($block['settings'], true);
        
        $dbSettings = $this->postBlockModel->getBlockSettings($block['type']);
        
        $mergedSettings = array_merge($dbSettings, $settings);
        
        $processedContent = $this->postBlockManager->processPostBlockContent(
            $content, 
            $block['type'], 
            $mergedSettings
        );
        
        return [
            'type' => $block['type'],
            'content' => $processedContent,
            'settings' => $mergedSettings
        ];
    }
    
    /**
     * Декодирует строку из JSON, если это необходимо
     * 
     * @param mixed $data Данные для декодирования
     * @param bool $defaultArray Возвращать массив по умолчанию
     * @return mixed Декодированные данные
     */
    private function decodeJsonIfNeeded($data, $defaultArray = false) {
        
        if (is_string($data)) {
            $cleaned = stripslashes($data);
            $decoded = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            } else {
                $decoded = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }
        
        return $defaultArray ? [] : $data;
    }
    
    /**
     * Подготавливает пользовательские поля для сущности
     * 
     * @param string $entityType Тип сущности (например, 'page')
     * @param int $entityId ID сущности
     * @return array Массив значений полей с метаданными
     */
    private function prepareCustomFields($entityType, $entityId) {
        $fieldModel = new \FieldModel($this->db);
        $customFields = $fieldModel->getActiveByEntityType($entityType);
        
        $fieldValues = [];
        foreach ($customFields as $field) {
            $fieldValues[$field['system_name']] = [
                'value' => $fieldModel->getFieldValue($entityType, $entityId, $field['system_name']),
                'field' => $field
            ];
        }
        
        return $fieldValues;
    }
    
    /**
     * Обрабатывает ошибки при загрузке страницы
     * 
     * @param string $message Сообщение об ошибке
     * @param string $redirectUrl URL для перенаправления
     * @return void
     */
    private function handleError($message, $redirectUrl) {
        \Notification::error($message);
        $this->redirect($redirectUrl);
    }
}