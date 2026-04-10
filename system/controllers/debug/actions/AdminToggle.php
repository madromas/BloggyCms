<?php

namespace debug\actions;

/**
 * Действие переключения режима отладки (AJAX)
 * @package debug\actions
 */
class AdminToggle extends DebugAction {
    
    public function execute() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Только AJAX запросы']);
            return;
        }
        
        $currentState = \SettingsHelper::get('general', 'debug_mode', false);
        $newState = !$currentState;
        
        $settingsModel = new \SettingsModel($this->db);
        $generalSettings = $settingsModel->get('general');
        $generalSettings['debug_mode'] = $newState;
        $result = $settingsModel->save('general', $generalSettings);
        
        if ($result) {

            \DebugHandler::init($newState, $this->db);
            \SettingsHelper::clearCache('general');
            
            $message = $newState ? '🐛 Режим отладки ВКЛЮЧЕН. Все ошибки будут сохраняться в лог.' : 'Режим отладки ВЫКЛЮЧЕН. Логирование ошибок остановлено.';
            $_SESSION['toast'] = [
                'type' => $newState ? 'success' : 'warning',
                'message' => $message
            ];
        }
        
        $this->jsonResponse([
            'success' => $result,
            'debug_enabled' => $newState,
            'message' => $result 
                ? ($newState ? 'Режим отладки включен' : 'Режим отладки выключен')
                : 'Ошибка при изменении состояния'
        ]);
    }
}