<?php

namespace icons\actions;

/**
* Действие получения данных об иконках через AJAX
* @package icons\actions
*/
class AdminIconsData {
    
    protected $controller;
    
    /**
    * Установка контроллера для действия
    * @param object $controller Объект контроллера
    * @return void
    */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    /**
    * Метод выполнения получения данных об иконках 
    * @return void
    */
    public function execute() {
        try {
            header('Content-Type: application/json');
            
            $icons = $this->getAllIcons();
            
            if (empty($icons)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Иконки не найдены',
                    'data' => []
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Иконки успешно загружены',
                'data' => $icons
            ]);
            
        } catch (\Exception $e) {
            \Logger::error('Ошибка при загрузке иконок: ' . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при загрузке иконок: ' . $e->getMessage(),
                'data' => []
            ]);
        }
        exit;
    }
    
    /**
    * Получение всех иконок из директорий шаблона
    * @return array Структурированный массив с информацией об иконках
    */
    private function getAllIcons() {
        $icons = [];
        
        $templates = $this->getAvailableTemplates();
        
        foreach ($templates as $template) {
            $iconsDir = TEMPLATES_PATH . '/' . $template . '/admin/icons/';
            
            if (!is_dir($iconsDir)) {
                continue;
            }
            
            $files = glob($iconsDir . '*.svg');
            
            foreach ($files as $file) {
                $set = basename($file, '.svg');
                $content = file_get_contents($file);
                
                preg_match_all('/<symbol\s+id="([^"]+)"/', $content, $matches);
                
                if (!empty($matches[1])) {
                    $icons[$template][$set] = [
                        'name' => $set,
                        'template' => $template,
                        'path' => '/templates/' . $template . '/admin/icons/' . $set . '.svg',
                        'icons' => array_map(function($id) use ($set, $template) {
                            return [
                                'id' => $id,
                                'preview' => $this->getIconPreviewHtml($set, $id, $template)
                            ];
                        }, $matches[1])
                    ];
                }
            }
        }
        
        if (empty($icons)) {
            throw new \Exception('В системе не найдено ни одного файла с иконками');
        }
        
        return $icons;
    }
    
    /**
    * Получение списка доступных шаблонов
    * @return array Массив с названиями шаблонов
    */
    private function getAvailableTemplates() {
        $templates = ['default'];
        
        $templatesDir = TEMPLATES_PATH;
        if (is_dir($templatesDir)) {
            $items = scandir($templatesDir);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && is_dir($templatesDir . '/' . $item)) {
                    $templates[] = $item;
                }
            }
        }
        
        return array_unique($templates);
    }
    
    /**
    * Генерация HTML для предварительного просмотра иконки
    * @param string $set Набор иконок (имя файла без расширения)
    * @param string $iconId ID иконки в SVG файле
    * @param string $template Название шаблона
    * @return string HTML-код для отображения иконки
    */
    private function getIconPreviewHtml($set, $iconId, $template = 'default') {
        return sprintf(
            '<svg width="24" height="24" style="fill: #2d5f94;"><use href="%s#%s"/></svg>',
            BASE_URL . '/templates/' . $template . '/admin/icons/' . $set . '.svg',
            html($iconId)
        );
    }
}