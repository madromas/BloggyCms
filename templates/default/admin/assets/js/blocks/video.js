(function() {
    'use strict';
    
    function initVideoBlockForm(container) {
        if (!container || container._videoBlockInitialized) return;
        container._videoBlockInitialized = true;
        
        const typeSelect = container.querySelector('[data-video-type-select]');
        const uploadSection = container.querySelector('[data-upload-section]');
        const externalSection = container.querySelector('[data-external-section]');
        const urlLabel = container.querySelector('[data-url-label]');
        const urlHint = container.querySelector('[data-url-hint]');
        const urlInput = container.querySelector('[data-video-url-input]');
        const fileInput = container.querySelector('[data-video-file]');
        const videoUrlHidden = container.querySelector('[data-video-url-hidden]');
        
        if (!typeSelect) return;
        
        function updateVisibility() {
            const type = typeSelect.value;
            
            if (uploadSection) {
                uploadSection.style.display = type === 'upload' ? 'block' : 'none';
            }
            if (externalSection) {
                externalSection.style.display = type !== 'upload' ? 'block' : 'none';
            }
            
            if (type === 'rutube') {
                if (urlLabel) urlLabel.textContent = 'Ссылка на видео Rutube';
                if (urlHint) urlHint.innerHTML = 'Пример: https://rutube.ru/video/private/xxx/ или https://rutube.ru/video/xxx/';
                if (urlInput) urlInput.placeholder = 'https://rutube.ru/video/...';
            } else if (type === 'vk') {
                if (urlLabel) urlLabel.textContent = 'Ссылка на видео VK';
                if (urlHint) urlHint.innerHTML = 'Пример: https://vk.com/video-xxx_xxx или https://vk.com/video?z=video-xxx_xxx';
                if (urlInput) urlInput.placeholder = 'https://vk.com/video...';
            }
            
            // Обновляем required атрибуты
            if (fileInput) {
                fileInput.required = (type === 'upload');
            }
            if (urlInput) {
                urlInput.required = (type !== 'upload');
            }
        }
        
        typeSelect.addEventListener('change', updateVisibility);
        
        updateVisibility();
        
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.value && videoUrlHidden) {}
            });
        }
        
        if (urlInput && videoUrlHidden) {
            urlInput.addEventListener('input', function() {
                if (typeSelect.value !== 'upload') {
                    videoUrlHidden.value = this.value;
                }
            });
        }
    }
    
    function initAllVideoBlocks() {
        const containers = document.querySelectorAll('[data-video-block]');
        containers.forEach(container => initVideoBlockForm(container));
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllVideoBlocks);
    } else {
        initAllVideoBlocks();
    }
    
    if (typeof window.MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.hasAttribute && node.hasAttribute('data-video-block')) {
                            initVideoBlockForm(node);
                        }

                        if (node.querySelectorAll) {
                            const nestedContainers = node.querySelectorAll('[data-video-block]');
                            nestedContainers.forEach(container => initVideoBlockForm(container));
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
})();