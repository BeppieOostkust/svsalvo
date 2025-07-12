<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onderhoud - SSV De Moes</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .maintenance-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .progress-bar {
            animation: progress 8s ease-in-out infinite;
        }
        
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 0%; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Logo/Icon -->
        <div class="maintenance-animation mb-8">
            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mb-6 shadow-2xl">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            
            <!-- Site Title -->
            <h1 class="text-4xl font-bold text-gray-900 mb-2">SSV De Moes</h1>
            <p class="text-lg text-gray-600">Schietvereniging</p>
        </div>
        
        <!-- Maintenance Message -->
        <div class="bg-white rounded-3xl shadow-2xl p-10 mb-8 border border-gray-100">
            <div class="text-6xl mb-6">🔧</div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                Website in Onderhoud
            </h2>
            
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                We werken momenteel aan spannende verbeteringen voor onze website. 
                <br class="hidden md:block">
                Bedankt voor je geduld, we zijn zo snel mogelijk weer online!
            </p>
            
            <!-- Progress Bar -->
            <div class="bg-gray-200 rounded-full h-3 mb-6">
                <div class="progress-bar bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 rounded-xl p-4">
                    <div class="text-2xl mb-2">⚡</div>
                    <h3 class="font-semibold text-gray-900">Sneller</h3>
                    <p class="text-sm text-gray-600">Verbeterde prestaties</p>
                </div>
                
                <div class="bg-green-50 rounded-xl p-4">
                    <div class="text-2xl mb-2">🎯</div>
                    <h3 class="font-semibold text-gray-900">Beter</h3>
                    <p class="text-sm text-gray-600">Nieuwe functies</p>
                </div>
                
                <div class="bg-purple-50 rounded-xl p-4">
                    <div class="text-2xl mb-2">🔒</div>
                    <h3 class="font-semibold text-gray-900">Veiliger</h3>
                    <p class="text-sm text-gray-600">Extra beveiliging</p>
                </div>
            </div>
            
            @if(isset($retryAfter) && $retryAfter)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-blue-800 font-medium">
                        Verwachte tijd: {{ $retryAfter }}
                    </span>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Contact Information -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">
                📞 Heb je vragen?
            </h3>
            
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="flex items-center justify-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <a href="mailto:info@ssvdemoes.nl" class="text-blue-600 hover:text-blue-800 font-medium">
                            info@ssvdemoes.nl
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-gray-100">
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    De Schacht 5, 5107 RD Dongen, Nederland
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                © {{ date('Y') }} SSV De Moes. We zijn zo snel mogelijk terug! 🎯
            </p>
        </div>
    </div>
    
    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh elke 60 seconden om te controleren of de site weer online is
        let refreshInterval = setInterval(function() {
            window.location.reload();
        }, 60000);
        
        // Countdown timer (optioneel)
        let countdown = 60;
        const countdownEl = document.createElement('div');
        countdownEl.className = 'fixed bottom-4 right-4 bg-white shadow-lg rounded-lg px-4 py-2 text-sm text-gray-600';
        countdownEl.innerHTML = `Automatisch verversen over: <span class="font-bold">${countdown}s</span>`;
        document.body.appendChild(countdownEl);
        
        const timer = setInterval(() => {
            countdown--;
            countdownEl.innerHTML = `Automatisch verversen over: <span class="font-bold">${countdown}s</span>`;
            
            if (countdown <= 0) {
                countdown = 60;
            }
        }, 1000);
        
        // Manual refresh button
        countdownEl.addEventListener('click', () => {
            window.location.reload();
        });
        countdownEl.style.cursor = 'pointer';
        countdownEl.title = 'Klik om handmatig te verversen';
    </script>
</body>
</html>
