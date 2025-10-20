<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erkek Üfleyici - Kız Uyanan Animasyonu (Saç Düzeltildi)</title>
    <style>
        :root {
            /* Renkler */
            --face-bg-blower: #b2e0b2; /* Erkek üfleyici yüz rengi */
            --face-bg-sleeper: #ffdd95; /* Kız uyanan yüz rengi */
            --eye-color: #553b00;
            --mouth-color: #a37b1b;
            --hair-color-blower: #2d5b2d; /* Erkek saç rengi */
            --hair-color-sleeper: #cd5c5c; /* Kız saç rengi (açık kızıl) */
            --button-bg: #87ceeb;
            --button-hover: #6fb8d8;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e0ffe0;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .scene {
            display: flex;
            align-items: flex-end;
            gap: 80px;
            position: relative;
        }

        .character-container {
            text-align: center;
            position: relative;
            transform-origin: bottom center;
        }
        
        .character-container.sleeper-initial {
             animation: initial-sway 4s ease-in-out infinite;
        }

        .character {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            position: relative;
            margin-bottom: 20px;
        }

        .face {
            width: 100%;
            height: 100%;
            background-color: var(--face-bg-blower); /* Varsayılan renk */
            border-radius: 50%;
            border: 4px solid var(--eye-color);
            position: relative;
            /* overflow: hidden;  Artık saçları kesmemesi için kaldırıldı */
        }

        /* --- Ortak Saç Stilleri için kapsayıcı --- */
        .hair {
            position: absolute;
            top: -10px; /* Yüzün üst kısmından başlar */
            left: 50%;
            transform: translateX(-50%);
            transform-origin: bottom center;
            transition: transform 0.4s ease-out;
            width: 100%; /* Karakter genişliği kadar */
            height: 50px; /* Saçın kapsayacağı max yükseklik */
            z-index: 1; /* Yüzün arkasında, gözlerin önünde */
        }

        .hair-strand {
            position: absolute;
            background-color: var(--hair-color-blower); /* Varsayılan renk */
            border-radius: 4px;
            bottom: 0; /* Kapsayıcının altından başlar */
            transform-origin: bottom center;
        }


        /* --- Üfleyici Karakter (Erkek) Stilleri --- */
        .character-container.blower.blowing {
            animation: blow-action 0.9s ease-in-out forwards;
        }
        .character.blower .face { background-color: var(--face-bg-blower); }
        .character.blower .eye {
            position: absolute; top: 45%; width: 15px; height: 15px;
            background-color: var(--eye-color); border-radius: 50%;
            z-index: 2; /* Gözler saçın üzerinde olsun */
        }
        .character.blower .eye-left { left: 30%; }
        .character.blower .eye-right { right: 30%; }
        
        .character.blower .mouth {
            position: absolute; bottom: 25%; left: 50%; transform: translateX(-50%);
            width: 30px; height: 10px; border: 4px solid transparent;
            border-radius: 0 0 30px 30px; border-top-color: var(--mouth-color);
            transition: all 0.2s ease-in-out;
            box-sizing: border-box;
            z-index: 2; /* Ağız saçın üzerinde olsun */
        }
        
        .character.blower.blowing .mouth {
            width: 35px; height: 35px; border-radius: 50%;
            border: 4px solid var(--mouth-color); bottom: 20%;
            transform: translateX(-50%) translateY(5px);
            animation: puff-mouth 0.3s forwards;
        }

        /* Erkek Saç Stili - Daha Kısa ve Yüze Yakın */
        .character.blower .hair-strand {
            background-color: var(--hair-color-blower);
            width: 10px;
            height: 25px;
            border-radius: 5px;
        }
        .character.blower .hair-strand-1 { left: 25%; transform: rotate(-20deg); }
        .character.blower .hair-strand-2 { left: 50%; transform: translateX(-50%) rotate(0deg); }
        .character.blower .hair-strand-3 { left: 75%; transform: rotate(20deg); }


        /* --- Uykulu Karakter (Kız) Stilleri --- */
        .character.sleeper .face { background-color: var(--face-bg-sleeper); }
        .character.sleeper .eye {
            position: absolute; top: 45%; width: 10px; height: 10px;
            background-color: var(--eye-color); border-radius: 50%;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 2; /* Gözler saçın üzerinde olsun */
        }
        .character.sleeper .eye-left { left: 30%; }
        .character.sleeper .eye-right { right: 30%; }
        .character.sleeper .eye.closed {
            top: 50%; height: 4px; width: 15px; border-radius: 2px;
        }
        .character.sleeper.awake .eye.closed {
            height: 20px; width: 20px;
        }

        .character.sleeper .mouth {
            position: absolute; bottom: 25%; left: 50%; transform: translateX(-50%);
            width: 40px; height: 20px; border: 4px solid transparent;
            border-bottom-color: var(--mouth-color); border-radius: 0 0 40px 40px;
            z-index: 2; /* Ağız saçın üzerinde olsun */
        }

        /* Kız Saç Stili - Daha Uzun ve Yanlara Doğru */
        .character.sleeper .hair-strand {
            background-color: var(--hair-color-sleeper);
            width: 8px;
            height: 40px; /* Daha uzun saç */
            border-radius: 4px;
        }
        .character.sleeper .hair-strand-1 { left: 20%; transform: rotate(-30deg); }
        .character.sleeper .hair-strand-2 { left: 50%; transform: translateX(-50%) rotate(5deg); }
        .character.sleeper .hair-strand-3 { left: 80%; transform: rotate(30deg); }

        /* Uyanma anında saçın hareketlenmesi */
        .character.sleeper.awake .hair-strand-1 { animation: hair-blow-left 0.8s ease-out forwards; }
        .character.sleeper.awake .hair-strand-2 { animation: hair-blow-center 0.8s ease-out 0.1s forwards; }
        .character.sleeper.awake .hair-strand-3 { animation: hair-blow-right 0.8s ease-out 0.2s forwards; }

        /* --- Rüzgar Efekti --- */
        .wind {
            position: absolute; top: 50%; left: 50%; width: 50px; height: 4px;
            background-color: var(--button-bg); border-radius: 2px; opacity: 0;
            z-index: 10;
        }
        .blowing-winds .wind-1 { animation: whoosh 0.8s ease-out 0.2s forwards; }
        .blowing-winds .wind-2 { animation: whoosh 0.9s ease-out 0.3s forwards; transform: translateY(20px) scale(0.8); }
        .blowing-winds .wind-3 { animation: whoosh 0.8s ease-out 0.35s forwards; transform: translateY(-15px) scale(0.9); }


        /* --- KEYFRAMES --- */
        @keyframes initial-sway {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(3deg); }
        }

        @keyframes blow-action {
            0%   { transform: translateX(0) rotate(0deg); }
            30%  { transform: translateX(-20px) rotate(-5deg) scale(0.95); }
            70%  { transform: translateX(25px) rotate(8deg) scale(1.1); }
            100% { transform: translateX(0) rotate(0deg) scale(1); }
        }
        
        @keyframes pushed-back {
            0%   { transform: translateX(0) rotate(0); }
            30%  { transform: translateX(30px) rotate(10deg); }
            50%  { transform: translateX(-10px) rotate(-5deg); }
            70%  { transform: translateX(5px) rotate(3deg); }
            100% { transform: translateX(0) rotate(0); }
        }

        @keyframes whoosh {
            0%   { left: 50%; opacity: 1; width: 50px; }
            100% { left: 250px; opacity: 0; width: 200px; }
        }
        
        @keyframes puff-mouth {
            0%   { transform: translateX(-50%) translateY(0); }
            50%  { transform: translateX(-50%) translateY(5px) scale(1.1); }
            100% { transform: translateX(-50%) translateY(0); }
        }

        /* Uyanan kızın saç hareketleri */
        @keyframes hair-blow-left {
            0%   { transform: rotate(-30deg); }
            50%  { transform: translateY(-10px) rotate(-70deg) scale(1.2); }
            100% { transform: rotate(-30deg); }
        }
        @keyframes hair-blow-center {
            0%   { transform: translateX(-50%) rotate(5deg); }
            50%  { transform: translateX(-50%) translateY(-15px) rotate(-30deg) scale(1.3); }
            100% { transform: translateX(-50%) rotate(5deg); }
        }
        @keyframes hair-blow-right {
            0%   { transform: rotate(30deg); }
            50%  { transform: translateY(-10px) rotate(60deg) scale(1.2); }
            100% { transform: rotate(30deg); }
        }


        /* --- Buton --- */
        button {
            position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%);
            padding: 15px 30px; font-size: 1.2em; font-weight: bold;
            color: var(--eye-color); background-color: var(--button-bg); border: none;
            border-radius: 10px; cursor: pointer; transition: background-color 0.2s; z-index: 20;
        }
        button:hover { background-color: var(--button-hover); }

    </style>
</head>
<body>

<div class="scene">
    <div class="character-container blower" id="blower-container">
        <div class="character blower" id="blower-character">
            <div class="face">
                <div class="eye eye-left"></div>
                <div class="eye eye-right"></div>
                <div class="mouth"></div>
            </div>
            <div class="hair">
                <div class="hair-strand hair-strand-1"></div>
                <div class="hair-strand hair-strand-2"></div>
                <div class="hair-strand hair-strand-3"></div>
            </div>
            <div class="wind-effect-origin">
                <div class="wind wind-1"></div>
                <div class="wind wind-2"></div>
                <div class="wind wind-3"></div>
            </div>
        </div>
    </div>
    <div class="character-container sleeper-initial" id="sleeper-container">
        <div class="character sleeper" id="sleeper-character">
            <div class="face">
                <div class="eye eye-left closed"></div>
                <div class="eye eye-right closed"></div>
                <div class="mouth"></div>
            </div>
            <div class="hair">
                <div class="hair-strand hair-strand-1"></div>
                <div class="hair-strand hair-strand-2"></div>
                <div class="hair-strand hair-strand-3"></div>
            </div>
        </div>
    </div>
</div>

<button id="blow-button">Üfle!</button>

<audio id="whoosh-sound" src="https://www.soundjay.com/misc/sounds/wind-whoosh-01.mp3" preload="auto"></audio>

<script>
    const blowButton = document.getElementById('blow-button');
    const blowerContainer = document.getElementById('blower-container');
    const blowerCharacter = document.getElementById('blower-character');
    const sleeperContainer = document.getElementById('sleeper-container');
    const sleeperCharacter = document.getElementById('sleeper-character');
    const whooshSound = document.getElementById('whoosh-sound');
    const windEffectOrigin = blowerCharacter.querySelector('.wind-effect-origin');

    let isBlowing = false;

    blowButton.addEventListener('click', () => {
        if (isBlowing) return;
        isBlowing = true;

        sleeperContainer.classList.remove('sleeper-initial'); 

        blowerContainer.classList.add('blowing');
        blowerCharacter.classList.add('blowing');
        windEffectOrigin.classList.add('blowing-winds');

        setTimeout(() => {
            sleeperContainer.classList.add('awake');
            sleeperCharacter.classList.add('awake');
        }, 300); 

        whooshSound.currentTime = 0;
        whooshSound.play();

        setTimeout(() => {
            blowerContainer.classList.remove('blowing');
            blowerCharacter.classList.remove('blowing');
            windEffectOrigin.classList.remove('blowing-winds');
            sleeperContainer.classList.remove('awake');
            sleeperCharacter.classList.remove('awake');
            
            sleeperContainer.classList.add('sleeper-initial');
            isBlowing = false;
        }, 1800);
    });
</script>

</body>
</html>