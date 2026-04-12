class CustomAudioPlayer {
    constructor(container) {
        this.container = container;
        this.audio = container.querySelector('.audio-source');
        this.playPauseBtn = container.querySelector('[data-play-pause]');
        this.playIcon = container.querySelector('.play-icon');
        this.pauseIcon = container.querySelector('.pause-icon');
        this.progressSlider = container.querySelector('[data-progress-slider]');
        this.progress = container.querySelector('[data-progress]');
        this.buffered = container.querySelector('[data-buffered]');
        this.currentTimeEl = container.querySelector('[data-current-time]');
        this.durationEl = container.querySelector('[data-duration]');
        this.volumeBtn = container.querySelector('[data-volume-btn]');
        this.volumeSlider = container.querySelector('[data-volume-slider]');
        this.volumeHighIcon = container.querySelector('.volume-high-icon');
        this.volumeLowIcon = container.querySelector('.volume-low-icon');
        this.volumeMutedIcon = container.querySelector('.volume-muted-icon');
        
        this.init();
    }
    
    init() {

        this.playPauseBtn.addEventListener('click', () => this.togglePlay());
        this.progressSlider.addEventListener('input', () => this.seek());
        this.volumeSlider.addEventListener('input', () => this.setVolume());
        this.volumeBtn.addEventListener('click', () => this.toggleMute());
        this.audio.addEventListener('loadedmetadata', () => this.updateDuration());
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('progress', () => this.updateBuffered());
        this.audio.addEventListener('play', () => this.onPlay());
        this.audio.addEventListener('pause', () => this.onPause());
        this.audio.addEventListener('ended', () => this.onEnded());
        this.audio.addEventListener('volumechange', () => this.updateVolumeUI());
        this.updateVolumeUI();
        
        if (this.audio.readyState >= 1) {
            this.updateDuration();
        }
    }
    
    togglePlay() {
        if (this.audio.paused) {
            this.audio.play();
        } else {
            this.audio.pause();
        }
    }
    
    onPlay() {
        this.playIcon.style.display = 'none';
        this.pauseIcon.style.display = 'block';
        this.container.classList.add('playing');
    }
    
    onPause() {
        this.playIcon.style.display = 'block';
        this.pauseIcon.style.display = 'none';
        this.container.classList.remove('playing');
    }
    
    onEnded() {
        this.playIcon.style.display = 'block';
        this.pauseIcon.style.display = 'none';
        this.container.classList.remove('playing');
    }
    
    seek() {
        const seekTime = (this.progressSlider.value / 100) * this.audio.duration;
        this.audio.currentTime = seekTime;
    }
    
    updateProgress() {
        if (this.audio.duration) {
            const percent = (this.audio.currentTime / this.audio.duration) * 100;
            this.progressSlider.value = percent;
            this.progress.style.width = percent + '%';
            this.updateCurrentTime();
        }
    }
    
    updateBuffered() {
        if (this.audio.buffered.length > 0) {
            const bufferedEnd = this.audio.buffered.end(this.audio.buffered.length - 1);
            const percent = (bufferedEnd / this.audio.duration) * 100;
            this.buffered.style.width = percent + '%';
        }
    }
    
    updateDuration() {
        if (this.audio.duration && !isNaN(this.audio.duration)) {
            this.durationEl.textContent = this.formatTime(this.audio.duration);
        }
    }
    
    updateCurrentTime() {
        this.currentTimeEl.textContent = this.formatTime(this.audio.currentTime);
    }
    
    setVolume() {
        this.audio.volume = this.volumeSlider.value / 100;
        this.updateVolumeUI();
    }
    
    toggleMute() {
        this.audio.muted = !this.audio.muted;
        this.updateVolumeUI();
    }
    
    updateVolumeUI() {
        const volume = this.audio.volume * 100;
        const isMuted = this.audio.muted;
        
        this.volumeSlider.value = isMuted ? 0 : volume;
        
        this.volumeHighIcon.style.display = 'none';
        this.volumeLowIcon.style.display = 'none';
        this.volumeMutedIcon.style.display = 'none';
        
        if (isMuted || volume === 0) {
            this.volumeMutedIcon.style.display = 'block';
        } else if (volume < 50) {
            this.volumeLowIcon.style.display = 'block';
        } else {
            this.volumeHighIcon.style.display = 'block';
        }
    }
    
    formatTime(seconds) {
        if (isNaN(seconds)) return '0:00';
        
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return mins + ':' + (secs < 10 ? '0' : '') + secs;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const players = document.querySelectorAll('[data-audio-player]');
    players.forEach(container => {
        new CustomAudioPlayer(container);
    });
});

if (typeof window.MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    const players = node.querySelectorAll ? node.querySelectorAll('[data-audio-player]') : [];
                    players.forEach(container => {
                        if (!container._audioPlayerInitialized) {
                            container._audioPlayerInitialized = true;
                            new CustomAudioPlayer(container);
                        }
                    });
                    
                    if (node.hasAttribute && node.hasAttribute('data-audio-player') && !node._audioPlayerInitialized) {
                        node._audioPlayerInitialized = true;
                        new CustomAudioPlayer(node);
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