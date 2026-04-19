document.addEventListener('DOMContentLoaded', function() {
    initCodeBlocks();
});

function initCodeBlocks() {
    const codeBlocks = document.querySelectorAll('.code-block-wrapper');
    
    codeBlocks.forEach((block) => {
        addWindowControls(block);
        initCopyButton(block);
        addLineNumbers(block);
        toggleFilename(block);
    });
}

function addWindowControls(block) {
    const header = block.querySelector('.code-header .code-meta');
    if (!header) return;
    
    const windowControls = document.createElement('div');
    windowControls.className = 'code-window-controls';
    windowControls.innerHTML = `
        <div class="window-dot close"></div>
        <div class="window-dot minimize"></div>
        <div class="window-dot maximize"></div>
    `;
    
    header.prepend(windowControls);
}

function initCopyButton(block) {
    const copyBtn = block.querySelector('.btn-copy-code');
    if (!copyBtn) return;
    
    const codeContainer = block.querySelector('code');
    if (!codeContainer) return;
    
    const codeText = codeContainer.textContent || codeContainer.innerText;
    
    const copyText = copyBtn.querySelector('.btn-copy-text');
    const copySuccess = copyBtn.querySelector('.btn-copy-success');
    
    if (!copyText || !copySuccess) return;
    
    copyBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (this.classList.contains('copied')) {
            return;
        }
        
        try {
            await navigator.clipboard.writeText(codeText);
            showCopySuccess(this, copyText, copySuccess);
            highlightBlockAnimation(block);
        } catch (err) {
            console.error('Ошибка копирования:', err);
            if (fallbackCopy(codeText)) {
                showCopySuccess(this, copyText, copySuccess);
            }
        }
    });
}

function showCopySuccess(button, copyText, copySuccess) {
    button.classList.add('copied');
    
    copyText.style.display = 'none';
    copySuccess.style.display = 'inline-flex';
    
    setTimeout(() => {
        button.classList.remove('copied');
        copyText.style.display = 'inline-flex';
        copySuccess.style.display = 'none';
    }, 2000);
}

function fallbackCopy(text) {
    try {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        textArea.style.top = '-9999px';
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        const success = document.execCommand('copy');
        document.body.removeChild(textArea);
        
        return success;
    } catch (err) {
        console.error('Fallback метод не сработал:', err);
        return false;
    }
}

function addLineNumbers(block) {
    const preElement = block.querySelector('pre');
    if (!preElement) return;
    
    if (preElement.classList.contains('line-numbers')) {
        const code = preElement.querySelector('code');
        if (!code) return;
        
        const lines = code.textContent.split('\n');
        const lineNumbers = lines.map((_, i) => i + 1).join('\n');
        
        preElement.setAttribute('data-line-numbers', lineNumbers);
    }
}

function highlightBlockAnimation(block) {
    const codeContainer = block.querySelector('.code-container');
    if (!codeContainer) return;
    
    codeContainer.classList.add('highlight-copied');
    
    setTimeout(() => {
        codeContainer.classList.remove('highlight-copied');
    }, 1500);
}

function toggleFilename(block) {
    const filenameElement = block.querySelector('.code-filename');
    if (filenameElement) {
        const filenameText = filenameElement.textContent.trim();
        if (filenameText) {
            block.classList.add('has-filename');
        } else {
            filenameElement.style.display = 'none';
        }
    }
}

window.CodeBlock = {
    init: initCodeBlocks,
    copyToClipboard: async function(codeText) {
        try {
            await navigator.clipboard.writeText(codeText);
            return true;
        } catch (err) {
            return fallbackCopy(codeText);
        }
    }
};