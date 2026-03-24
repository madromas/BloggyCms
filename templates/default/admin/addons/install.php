<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'cloud-upload', '24', '#000', 'me-2'); ?>
            Установка пакета
        </h4>
        <a href="<?php echo ADMIN_URL; ?>/addons" class="btn btn-outline-secondary btn-sm">
            <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
            Назад к пакетам
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <?php echo bloggy_icon('bs', 'info-circle-fill', '20', '#000', 'me-2'); ?>
                    <div>
                        <strong>Информация о пакете</strong><br>
                        Пакет должен иметь следующую структуру:
                        <ul class="mb-0 mt-2">
                            <li><code>files/</code> - папка с файлами (структура соответствует корню сайта)</li>
                            <li><code>install.php</code> - скрипт установки (опционально)</li>
                            <li><code>package.ini</code> - файл с описанием пакета</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="upload-area border-2 border-dashed rounded-3 p-5 text-center position-relative"
                id="uploadArea"
                style="border-color: #dee2e6; border-style: dashed; background: #f8f9fa; transition: all 0.3s ease;">
                
                <div class="upload-default" id="uploadDefault">
                    <div class="mb-3">
                        <?php echo bloggy_icon('bs', 'file-zip', '48', '#6C6C6C'); ?>
                    </div>
                    <h5 class="text-muted mb-2">Перетащите ZIP-архив сюда</h5>
                    <p class="text-muted small mb-3">или</p>
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addon-file-input').click()">
                        <?php echo bloggy_icon('bs', 'folder2-open', '16', '#fff', 'me-1'); ?>
                        Выберите файл
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">Максимальный размер: 50MB</small>
                    </div>
                </div>
                
                <div class="upload-preview d-none" id="uploadPreview">
                    <div class="mb-3">
                        <?php echo bloggy_icon('bs', 'file-zip', '48', '#198754'); ?>
                    </div>
                    <h5 class="text-success mb-2">Файл выбран</h5>
                    <p class="text-muted" id="fileName"></p>
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetUpload()">
                            <?php echo bloggy_icon('bs', 'arrow-repeat', '14', '#000', 'me-1'); ?>
                            Выбрать другой файл
                        </button>
                    </div>
                </div>
                
                <div class="package-preview d-none mt-4" id="packagePreview">
                    <div class="card border-0 bg-light">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0">
                                <?php echo bloggy_icon('bs', 'info-circle', '16', '#0d6efd', 'me-1'); ?>
                                Информация о пакете
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="120"><strong>Название:</strong></td>
                                            <td id="preview-title">—</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Версия:</strong></td>
                                            <td id="preview-version">—</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Тип:</strong></td>
                                            <td id="preview-type">—</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Автор:</strong></td>
                                            <td id="preview-author">—</td>
                                        </tr>
                                        <tr id="preview-email-row" style="display: none;">
                                            <td><strong>Email:</strong></td>
                                            <td id="preview-email">—</td>
                                        </tr>
                                        <tr id="preview-url-row" style="display: none;">
                                            <td><strong>Сайт:</strong></td>
                                            <td id="preview-url">—</td>
                                        </tr>
                                        <tr id="preview-description-row" style="display: none;">
                                            <td><strong>Описание:</strong></td>
                                            <td id="preview-description">—</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div id="preview-status" class="alert alert-info mb-0">
                                        <?php echo bloggy_icon('bs', 'check-circle', '20', '#0d6efd', 'me-1'); ?>
                                        Готов к установке
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="upload-progress mt-3 d-none" id="uploadProgress">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                            role="progressbar" style="width: 0%" id="progressBar"></div>
                    </div>
                    <small class="text-muted mt-1" id="progressText">Загрузка...</small>
                </div>
                
                <div class="upload-result mt-3 d-none" id="uploadResult">
                    <div class="alert" id="resultMessage"></div>
                </div>
                
                <input type="file" class="d-none" id="addon-file-input" accept=".zip">
            </div>
            
            <div class="mt-4 d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="install-btn" disabled>
                    <?php echo bloggy_icon('bs', 'check-lg', '16', '#fff', 'me-2'); ?>
                    Установить
                </button>
            </div>
        </div>
    </div>
</div>

<?php add_admin_js('templates/default/admin/assets/js/controllers/addons.js'); ?>