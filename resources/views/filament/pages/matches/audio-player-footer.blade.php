<div>
    {{-- Hidden audio element --}}
    <audio id="audio-player" style="display: none;"></audio>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const audioPlayer = document.getElementById('audio-player');
            
            // Listen for the play-sound event
            window.addEventListener('play-sound', function (event) {
                const soundPath = event.detail.soundPath;
                if (soundPath) {
                    audioPlayer.src = soundPath;
                    audioPlayer.play().catch(error => {
                        console.error('Failed to play audio:', error);
                    });
                }
            });
        });
    </script>
</div>
