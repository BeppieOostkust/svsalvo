<div>
    <script>
        // Create a global audio manager to prevent Livewire interference
        window.AudioManager = window.AudioManager || {
            audioElement: null,
            isPlaying: false,
            
            init: function() {
                if (!this.audioElement) {
                    this.audioElement = new Audio();
                    this.audioElement.preload = 'auto';
                    console.log('Global audio manager initialized');
                }
            },
            
            playSound: function(soundPath) {
                if (!soundPath) return;
                
                console.log('Playing sound via AudioManager:', soundPath);
                
                this.init();
                
                // Stop any current playback
                if (!this.audioElement.paused) {
                    this.audioElement.pause();
                    this.audioElement.currentTime = 0;
                }
                
                // Set source and play
                this.audioElement.src = soundPath;
                this.isPlaying = true;
                
                const playPromise = this.audioElement.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        console.log('Audio started playing successfully');
                    }).catch(error => {
                        console.error('Failed to play audio:', error.name, error.message);
                        this.isPlaying = false;
                        
                        // Handle specific error types gracefully
                        if (error.name === 'AbortError') {
                            console.log('Audio was aborted - trying to replay...');
                            // Try to play again after a small delay
                            setTimeout(() => {
                                this.audioElement.src = soundPath;
                                this.audioElement.play().catch(e => {
                                    console.log('Retry failed:', e.name);
                                });
                            }, 100);
                        }
                    });
                }
                
                // Clean up when finished
                this.audioElement.onended = () => {
                    console.log('Audio finished playing');
                    this.isPlaying = false;
                };
            }
        };
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initializing audio manager...');
            window.AudioManager.init();
            
            // Listen for Livewire events
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', function () {
                    console.log('Livewire initialized, setting up play-sound listener');
                    Livewire.on('play-sound', function (data) {
                        console.log('Received play-sound event:', data);
                        const soundPath = data.soundPath || data[0]?.soundPath;
                        window.AudioManager.playSound(soundPath);
                    });
                });
            }
            
            // Also listen for browser events as fallback
            window.addEventListener('play-sound', function (event) {
                console.log('Received browser play-sound event:', event);
                const soundPath = event.detail.soundPath;
                window.AudioManager.playSound(soundPath);
            });
        });
    </script>
</div>
