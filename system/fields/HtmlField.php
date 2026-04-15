<?php

/**
* Поле типа "HTML-блок" для системы пользовательских полей
* @package Fields
*/
class HtmlField extends BaseField {
    
    private static $assetsLoaded = false;
    
    /**
    * Возвращает тип поля
    * @return string 'html'
    */
    public function getType(): string {
        return 'html';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'HTML-блок'
    */
    public function getName(): string {
        return 'HTML-блок';
    }
    
    /**
    * Загружает ресурсы Ace Editor
    */
    private function loadAceAssets(): void {
        if (self::$assetsLoaded) {
            return;
        }
        
        if (function_exists('admin_js')) {
            admin_js('templates/default/admin/assets/js/controllers/ace.js');
            admin_js('templates/default/admin/assets/js/controllers/mode-html.js');
            admin_js('templates/default/admin/assets/js/controllers/theme-monokai.js');
        }
        
        self::$assetsLoaded = true;
    }
    
    /**
    * Генерирует HTML для редактирования поля в форме 
    * @param mixed $value Текущее значение поля
    * @param string $entityType Тип сущности (post, user, category и т.д.)
    * @param int $entityId ID сущности
    * @return string HTML-код для редактирования
    */
    public function renderInput($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        $height = $this->config['height'] ?? 300;
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        $editorId = 'html-editor-' . $this->systemName . '-' . uniqid();
        
        $this->loadAceAssets();
        
        return '
            <div class="html-field-wrapper">
                <div id="' . $editorId . '" style="height: ' . $height . 'px; width: 100%; border: 1px solid #dee2e6; border-radius: 4px; position: relative;"></div>
                <textarea name="' . $fieldName . '" 
                          id="' . $editorId . '-textarea"
                          class="form-control d-none"
                          ' . $required . '>' . html($safeValue, ENT_QUOTES, 'UTF-8') . '</textarea>
                <div class="form-text mt-2">Поддерживается HTML разметка. Используйте редактор для удобного форматирования.</div>
            </div>
            <script>
            (function() {
                var initEditor = function() {
                    var editorId = "' . $editorId . '";
                    var container = document.getElementById(editorId);
                    var textarea = document.getElementById(editorId + "-textarea");
                    
                    if (!container || !textarea) {
                        console.warn("Editor container or textarea not found");
                        return;
                    }
                    
                    if (typeof ace === "undefined") {
                        console.warn("Ace editor not loaded yet, retrying...");
                        setTimeout(initEditor, 100);
                        return;
                    }
                    
                    if (container.hasAttribute("data-ace-initialized")) {
                        return;
                    }
                    
                    try {
                        var editor = ace.edit(editorId, {
                            theme: "ace/theme/monokai",
                            mode: "ace/mode/html",
                            showPrintMargin: false,
                            fontSize: "14px",
                            tabSize: 4,
                            useSoftTabs: true,
                            wrap: true,
                            minLines: 10,
                            maxLines: 30
                        });
                        
                        editor.setValue(textarea.value);
                        editor.clearSelection();
                        editor.resize(true);
                        
                        editor.session.on("change", function() {
                            textarea.value = editor.getValue();
                        });
                        
                        var form = textarea.closest("form");
                        if (form) {
                            form.addEventListener("submit", function() {
                                textarea.value = editor.getValue();
                            });
                        }
                        
                        container.setAttribute("data-ace-initialized", "true");
                        
                        // Принудительный resize после рендера
                        setTimeout(function() {
                            editor.resize();
                        }, 100);
                        
                    } catch(e) {
                        console.error("Ace editor error:", e);
                    }
                };
                
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", initEditor);
                } else {
                    initEditor();
                }
            })();
            </script>
        ';
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string Исходный HTML-код
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        if ($value === null || $value === '') {
            return '';
        }
        return $value;
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке 
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string Обрезанный текст без HTML-тегов
    */
    public function renderList($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        $stripped = strip_tags($safeValue);
        $truncated = mb_strlen($stripped) > 50 ? mb_substr($stripped, 0, 50) . '...' : $stripped;
        return "<span title='" . html($stripped, ENT_QUOTES, 'UTF-8') . "'>" . html($truncated, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        $height = html($this->config['height'] ?? '300', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Высота редактора (px)</label>
                        <input type='number' class='form-control' name='config[height]' value='{$height}' min='150' max='600' step='50'>
                        <div class='form-text'>Высота Ace Editor в пикселях</div>
                    </div>
                </div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Значение по умолчанию</label>
                <textarea class='form-control' name='config[default_value]' rows='4'>{$defaultValue}</textarea>
                <div class='form-text'>HTML-код, который будет предустановлен по умолчанию</div>
            </div>
        ";
    }
}