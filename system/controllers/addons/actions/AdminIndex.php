<?php

namespace addons\actions;

/**
 * Действие отображения списка установленных пакетов
 * 
 * @package addons\actions
 */
class AdminIndex extends AddonAction {
    
    /**
     * Метод выполнения
     */
    public function execute() {
        try {
            $addons = $this->addonModel->getAll();
            
            $hints = [
                "Пакеты должны иметь структуру: files/, install.php, package.ini",
                "При установке пакета создается резервная копия заменяемых файлов",
                "Перед удалением пакета рекомендуется сделать полную резервную копию сайта",
                "Убедитесь, что файлы пакета имеют правильные права доступа (755 для папок, 644 для файлов)",
                "Пакеты могут быть типов 'install' (установка) или 'update' (обновление)"
            ];
            
            $randomHint = $hints[array_rand($hints)];
            
            $this->render('admin/addons/index', [
                'addons' => $addons,
                'randomHint' => $randomHint,
                'addonCount' => count($addons),
                'pageTitle' => 'Управление пакетами'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка пакетов: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }
}
