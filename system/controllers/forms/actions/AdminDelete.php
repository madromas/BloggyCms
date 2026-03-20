<?php

namespace forms\actions;

/**
 * Действие удаления формы
 */
class AdminDelete extends FormAction {
    
    public function execute() {
        $id = $this->params['id'] ?? null;
        if (!$id) {
            \Notification::error('ID формы не указан');
            $this->redirect(ADMIN_URL . '/forms');
            return;
        }
        
        try {
            $form = $this->formModel->getById($id);
            if (!$form) {
                throw new \Exception('Форма не найдена');
            }
            
            // Удаляем форму (включая все связанные данные)
            $this->deleteFormWithRelations($id);
            
            \Notification::success('Форма успешно удалена');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении формы: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/forms');
    }
    
    /**
     * Удаляет форму со всеми связанными данными
     */
    private function deleteFormWithRelations($formId) {
        $db = $this->db;
        $db->beginTransaction();
        
        try {
            $submissions = $db->fetchAll(
                "SELECT id FROM form_submissions WHERE form_id = ?",
                [$formId]
            );
            
            foreach ($submissions as $submission) {
                $files = $db->fetchAll(
                    "SELECT * FROM form_files WHERE submission_id = ?",
                    [$submission['id']]
                );
                
                foreach ($files as $file) {
                    if (file_exists(ROOT_PATH . '/' . $file['file_path'])) {
                        unlink(ROOT_PATH . '/' . $file['file_path']);
                    }
                }
                
                $db->delete('form_files', ['submission_id' => $submission['id']]);
                $db->delete('form_submissions', ['id' => $submission['id']]);
            }
            
            $result = $db->delete('forms', ['id' => $formId]);
            
            if ($result === false) {
                throw new \Exception('Ошибка базы данных при удалении формы');
            }
            
            $db->commit();
            
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}