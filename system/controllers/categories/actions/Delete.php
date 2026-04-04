<?php

namespace categories\actions;

/**
* Действие удаления категории
* @package categories\actions
*/
class Delete extends CategoryAction {
    
    /**
    * Метод выполнения удаления категории
    * @return void
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID категории не указан');
            $this->redirect(ADMIN_URL . '/categories');
            return;
        }

        try {
            $category = $this->categoryModel->getById($id);
            
            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Категории', ADMIN_URL . '/categories');
            $this->addBreadcrumb('Удаление: ' . ($category ? $category['name'] : 'Категория #' . $id));
            
            $postsCount = $this->categoryModel->getPostsCount($id);
            
            if ($postsCount > 0) {
                if (isset($_POST['delete_action'])) {
                    $deleteAction = $_POST['delete_action'];
                    
                    if ($deleteAction === 'move_posts' && !empty($_POST['target_category_id'])) {
                        $targetCategoryId = (int)$_POST['target_category_id'];
                        
                        $this->categoryModel->movePostsToCategory($id, $targetCategoryId);
                        
                        if ($category && !empty($category['image'])) {
                            $imagePath = UPLOADS_PATH . '/images/' . $category['image'];
                            \FileUpload::delete($imagePath);
                        }
                        
                        $this->categoryModel->delete($id);
                        
                        $postsWord = get_numeric_ending($postsCount, ['пост', 'поста', 'постов']);
                        \Notification::success("Категория удалена. {$postsCount} {$postsWord} перемещены в выбранную категорию.");
                        
                    } 
                    elseif ($deleteAction === 'delete_all') {
                        $this->categoryModel->deleteWithPosts($id);
                        $postsWord = get_numeric_ending($postsCount, ['пост', 'поста', 'постов']);
                        \Notification::success("Категория и {$postsCount} {$postsWord} удалены.");
                        
                    } 
                    else {
                        \Notification::error('Не выбран способ удаления');
                        $this->redirect(ADMIN_URL . '/categories');
                        return;
                    }
                } 
                else {
                    $this->showDeleteOptions($id, $category, $postsCount);
                    return;
                }
                
            } 
            else {
                if ($category && !empty($category['image'])) {
                    $imagePath = UPLOADS_PATH . '/images/' . $category['image'];
                    \FileUpload::delete($imagePath);
                }
                
                $this->categoryModel->delete($id);
                \Notification::success('Категория успешно удалена');
            }
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении категории: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/categories');
    }
    
    /**
    * Отображение формы выбора способа удаления категории с постами
    * @param int $categoryId ID удаляемой категории
    * @param array|null $category Данные удаляемой категории
    * @param int $postsCount Количество постов в категории
    * @return void
    */
    private function showDeleteOptions($categoryId, $category, $postsCount) {
        $categories = $this->categoryModel->getAll();
        $otherCategories = array_filter($categories, function($cat) use ($categoryId) {
            return $cat['id'] != $categoryId;
        });
        
        $this->render('admin/categories/delete_options', [
            'category' => $category,
            'postsCount' => $postsCount,
            'otherCategories' => $otherCategories,
            'pageTitle' => 'Удаление категории'
        ]);
    }
}