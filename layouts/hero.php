<?php
// Load banners từ settings
try {
    $pdo = new PDO("mysql:host=localhost;dbname=du_an1;charset=utf8mb4", "root", "");
    $stmt = $pdo->query("SELECT k, v FROM settings WHERE k LIKE 'banner_%' ORDER BY k");
    $bannerSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $banners = [];
    foreach ($bannerSettings as $key => $value) {
        if (!empty($value) && trim($value) !== '') {
            $banners[] = $value;
        }
    }
} catch (Exception $e) {
    $banners = [];
}
?>

<!-- Hero Section / Banner Slideshow -->
<?php if (!empty($banners)): ?>
<section class="relative h-[500px] overflow-hidden bg-gray-900">
    <!-- Slideshow Container -->
    <div class="slideshow-container h-full relative">
        <?php foreach ($banners as $index => $banner): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?> absolute inset-0 transition-all duration-[1500ms] ease-in-out <?= $index === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105' ?>">
            <?php $bannerUrl = (strpos($banner, 'http') === 0) ? $banner : BASE_URL . $banner; ?>
            <img src="<?= htmlspecialchars($bannerUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Banner <?= $index + 1 ?>" class="slide-img w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        </div>
        <?php endforeach; ?>
        
        <!-- Text Overlay with Animation -->
        <div class="absolute inset-0 flex items-center z-10">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl hero-content">
                    <h1 class="hero-title text-4xl md:text-5xl lg:text-6xl font-black text-white mb-4 drop-shadow-lg">
                        Hương vị bừng tỉnh mỗi ngày
                    </h1>
                    <p class="hero-subtitle text-lg md:text-xl text-white/90 mb-8 drop-shadow">
                        Khám phá thế giới đồ uống đa dạng, từ cà phê đậm đà đến trà sữa ngọt ngào.
                    </p>
                    <a href="<?= BASE_URL ?>?action=products" class="hero-button inline-flex items-center gap-2 px-8 py-4 bg-white text-green-600 rounded-full font-bold text-lg hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 hover:scale-105">
                        <span>Đặt hàng ngay</span>
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Dots -->
        <?php if (count($banners) > 1): ?>
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-20">
            <?php foreach ($banners as $index => $banner): ?>
            <button onclick="currentSlide(<?= $index ?>)" class="dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all <?= $index === 0 ? 'bg-white w-8' : '' ?>"></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.slide {
    will-change: opacity, transform;
}

.slide-img {
    animation: kenburns 25s ease-in-out infinite;
    transition: transform 1.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide.active .slide-img {
    animation: kenburns 25s ease-in-out infinite;
}

@keyframes kenburns {
    0%, 100% {
        transform: scale(1) rotate(0deg);
    }
    25% {
        transform: scale(1.08) translateX(-2%);
    }
    50% {
        transform: scale(1.12) translateY(-2%);
    }
    75% {
        transform: scale(1.08) translateX(2%);
    }
}

.dot {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.dot:hover {
    transform: scale(1.2);
}

/* Text Animation */
.hero-content {
    animation: fadeInUp 1s ease-out;
}

.hero-title {
    animation: fadeInUp 1s ease-out 0.2s both;
}

.hero-subtitle {
    animation: fadeInUp 1s ease-out 0.4s both;
}

.hero-button {
    animation: fadeInUp 1s ease-out 0.6s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Parallax Effect on Scroll */
.slideshow-container {
    transition: transform 0.1s ease-out;
}
</style>

<script>
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let isTransitioning = false;

function showSlide(n) {
    if (isTransitioning) return;
    isTransitioning = true;
    
    slides.forEach((slide, i) => {
        if (i === n) {
            slide.classList.remove('opacity-0', 'scale-105');
            slide.classList.add('opacity-100', 'scale-100', 'active');
        } else {
            slide.classList.remove('opacity-100', 'scale-100', 'active');
            slide.classList.add('opacity-0', 'scale-105');
        }
    });
    
    dots.forEach((dot, i) => {
        if (i === n) {
            dot.classList.remove('bg-white/50', 'w-3');
            dot.classList.add('bg-white', 'w-8');
        } else {
            dot.classList.remove('bg-white', 'w-8');
            dot.classList.add('bg-white/50', 'w-3');
        }
    });
    
    setTimeout(() => {
        isTransitioning = false;
    }, 1500);
}

function currentSlide(n) {
    slideIndex = n;
    showSlide(slideIndex);
}

function nextSlide() {
    if (!isTransitioning) {
        slideIndex = (slideIndex + 1) % slides.length;
        showSlide(slideIndex);
    }
}

if (slides.length > 1) {
    setInterval(nextSlide, 6000);
}
</script>

<?php else: ?>
<!-- Default Hero nếu chưa có banner -->
<section class="relative h-[500px] bg-gradient-to-br from-green-400 via-green-300 to-green-200 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white rounded-full"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left z-10">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-4 drop-shadow-lg">
                    Hương vị bừng tỉnh mỗi ngày
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-8 drop-shadow">
                    Khám phá thế giới đồ uống đa dạng, từ cà phê đậm đà đến trà sữa ngọt ngào.
                </p>
                <a href="<?= BASE_URL ?>?action=products" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-600 rounded-full font-bold text-lg hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <span>Đặt hàng ngay</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            <!-- Right Image -->
            <div class="relative hidden lg:flex items-center justify-center">
                <!-- Bubble Tea Image -->
                <div class="relative z-10 transform hover:scale-105 transition-transform duration-300">
                    <div class="w-80 h-96 bg-gradient-to-b from-amber-100 to-amber-200 rounded-t-full rounded-b-3xl shadow-2xl relative overflow-hidden">
                        <!-- Milk Splash Effect -->
                        <div class="absolute bottom-0 left-0 right-0 h-32 bg-white rounded-t-full opacity-80"></div>
                        <div class="absolute bottom-16 left-1/2 -translate-x-1/2 w-24 h-24 bg-white rounded-full opacity-60"></div>
                        <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-white rounded-full opacity-40"></div>
                        <div class="absolute bottom-24 right-1/4 w-20 h-20 bg-white rounded-full opacity-50"></div>
                        
                        <!-- Boba Pearls -->
                        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                            <div class="w-8 h-8 bg-gray-800 rounded-full shadow-lg"></div>
                            <div class="w-8 h-8 bg-gray-800 rounded-full shadow-lg"></div>
                            <div class="w-8 h-8 bg-gray-800 rounded-full shadow-lg"></div>
                        </div>
                        <div class="absolute bottom-12 left-1/3 flex gap-2">
                            <div class="w-6 h-6 bg-gray-800 rounded-full shadow-lg"></div>
                            <div class="w-6 h-6 bg-gray-800 rounded-full shadow-lg"></div>
                        </div>
                        <div class="absolute bottom-14 right-1/3 flex gap-2">
                            <div class="w-7 h-7 bg-gray-800 rounded-full shadow-lg"></div>
                            <div class="w-7 h-7 bg-gray-800 rounded-full shadow-lg"></div>
                        </div>
                        
                        <!-- Straw -->
                        <div class="absolute top-0 right-1/3 w-4 h-40 bg-gradient-to-b from-red-400 to-red-500 rounded-full shadow-lg transform rotate-12"></div>
                    </div>
                </div>

                <!-- Floating Elements -->
                <div class="absolute top-10 left-10 w-16 h-16 bg-white/30 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                <div class="absolute bottom-20 right-10 w-12 h-12 bg-white/30 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
                <div class="absolute top-1/2 left-0 w-10 h-10 bg-white/30 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
            </div>
        </div>
    </div>

    <!-- Wave Bottom -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<style>
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-bounce {
    animation: bounce 3s ease-in-out infinite;
}
</style>

<?php endif; ?>
