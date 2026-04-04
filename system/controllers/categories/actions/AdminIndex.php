<?php

namespace categories\actions;

/**
* Действие для отображения списка категорий в админ-панели
* @package categories\actions
* @extends CategoryAction
*/
class AdminIndex extends CategoryAction {
    
    /**
    * Основной метод выполнения действия
    * @return void
    * @throws \Exception
    */
    public function execute() {
        try {

            $this->addBreadcrumb('Категории');
        
            $categories = $this->categoryModel->getAllOrdered();
            
            $hints = [
                "Перетаскивайте категории для изменения порядка отображения",
                "Категории с большим количеством постов отображаются выше",
                "Вы можете создавать вложенные категории перетаскиванием",
                "Используйте иконки для быстрого редактирования категорий",
                "Каждой категории можно назначить свое изображение",
                "Используйте описания категорий для улучшения SEO",
                "Категории помогают организовать контент по темам",
                "Вы можете установить пароль для приватных категорий",
                "Вы можете установить дополнительные поля для категорий и показывать их на сайте",
                "При удалении категории - если в ней присутствуют посты, система предложит перенести посты в другую категорию",
            ];
            
            $randomHint = $hints[array_rand($hints)];
            
            /**
            * Рендеринг шаблона админ-панели с передачей данных
            */
            $this->render('admin/categories/index', [
                'categories' => $categories,
                'randomHint' => $randomHint,
                'pageTitle' => 'Управление категориями'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке категорий: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }
}