'use strict';

var app = {
    selectedCities: [],
    hasRequestPending: false,

    // === Головна функція, яка показує картку ===
    updateForecastCard: function(data) {
        console.log('[App] Оновлюємо картку для', data.label);

        var card = document.querySelector('.cardTemplate');
        if (!card) return;

        var newCard = card.cloneNode(true);
        newCard.classList.remove('cardTemplate');
        newCard.classList.remove('hidden');
        newCard.style.display = 'block';

        newCard.querySelector('.city').textContent = data.label || 'Невідоме місто';
        newCard.querySelector('.temp').innerHTML = Math.round(data.currently.temperature) + '<sup>°C</sup>';
        newCard.querySelector('.condition').textContent = data.currently.summary || 'Ясно';

        // Заповнюємо високий/низький
        var high = data.daily ? Math.round(data.daily.data[0].temperatureMax) : 22;
        var low  = data.daily ? Math.round(data.daily.data[0].temperatureMin) : 10;
        newCard.querySelector('.high').textContent = '↑ ' + high + '°';
        newCard.querySelector('.low').textContent  = '↓ ' + low + '°';

        // Додаємо картку на сторінку
        document.querySelector('.main').appendChild(newCard);
    },

    // === Фіктивні дані (замість Firebase) ===
    getForecast: function(key, label) {
        var mockData = {
            newyork: {
                label: "New York, NY",
                currently: { temperature: 18, summary: "Clear" },
                daily: { data: [{ temperatureMax: 22, temperatureMin: 12 }] }
            },
            kyiv: {
                label: "Київ, Україна",
                currently: { temperature: 16, summary: "Ясно" },
                daily: { data: [{ temperatureMax: 19, temperatureMin: 10 }] }
            },
            lviv: {
                label: "Львів, Україна",
                currently: { temperature: 14, summary: "Хмарно" },
                daily: { data: [{ temperatureMax: 17, temperatureMin: 9 }] }
            }
        };

        var data = mockData[key] || mockData.newyork;
        data.key = key;
        app.updateForecastCard(data);
    }
};

// === Запуск застосунку ===
(function() {
    // Перший запуск
    app.getForecast('newyork');           // ← можна змінити на 'newyork' або 'lviv'

    // Реєстрація Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(() => console.log('Service Worker Registered'));
    }

    console.log('%c🚀 Погодний PWA запущено!', 'color:#2196F3; font-size:16px');
})();