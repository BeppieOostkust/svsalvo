<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onderhoud - SV Salvo</title>
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
        
        .loading-dots {
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0%, 20% { opacity: 0; }
            50% { opacity: 1; }
            80%, 100% { opacity: 0; }
        }
        
        .loading-dots:nth-child(2) {
            animation-delay: 0.3s;
        }
        
        .loading-dots:nth-child(3) {
            animation-delay: 0.6s;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Logo/Icon -->
        <div class="maintenance-animation mb-8">
            <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            
            <!-- Site Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-2">SV Salvo</h1>
        </div>
        
        <!-- Maintenance Message -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                🔧 Website in Onderhoud
            </h2>
            
            <p class="text-lg text-gray-600 mb-6">
                We werken momenteel aan verbeteringen voor onze website. 
                We zijn zo snel mogelijk weer online!
            </p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-blue-800 font-medium">
                        Verwachte tijd: {{ $retryAfter ?? 'Onbekend' }}
                    </span>
                </div>
            </div>
            
            <!-- Loading Animation -->
            <div class="flex justify-center items-center space-x-2 mb-6">
                <span class="text-gray-500">Bezig met updaten</span>
                <div class="flex space-x-1">
                    <div class="loading-dots w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="loading-dots w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="loading-dots w-2 h-2 bg-blue-500 rounded-full"></div>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                📞 Neem Contact Op
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <a href="mailto:secretaris@svsalvo.info" class="text-blue-600 hover:text-blue-800">
                        secretaris@svsalvo.info
                    </a>
                </div>
                
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+31162123456" class="text-blue-600 hover:text-blue-800">
                        0162 - 123 456
                    </a>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Schietvereniging Salvo • Geerbunders 31, 5461 XM Veghel
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                © {{ date('Y') }} SV Salvo. Alle rechten voorbehouden.
            </p>
        </div>
    </div>
    
    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh elke 30 seconden om te controleren of de site weer online is
        setTimeout(function() {
            window.location.reload();
        }, 30000);
        
        // Update de loading animatie
        setInterval(function() {
            const dots = document.querySelectorAll('.loading-dots');
            dots.forEach((dot, index) => {
                setTimeout(() => {
                    dot.style.animationDelay = (index * 0.3) + 's';
                }, index * 300);
            });
        }, 1500);
    </script>
</body>
</html>
