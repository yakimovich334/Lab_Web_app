<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Система управління готелем (SPA) - Назарій Якимович</title>
   
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="https://javascript.daypilot.org/demo/js/daypilot-all.min.js?v=2024"></script>
   
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .status-bar {
            display: inline-block;
            width: 6px;
            height: 40px;
            margin-right: 10px;
            border-radius: 3px;
            vertical-align: middle;
        }
        .bar-Dirty   { background-color: #e74c3c; }
        .bar-Cleanup { background-color: #f1c40f; }
        .bar-Ready   { background-color: #2ecc71; }
    </style>
</head>
<body>
<header>
    <div style="background:#2c3e50; color:white; padding:20px; border-bottom: 4px solid #3498db;">
        <h1>HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
        <p>AJAX'овий Календар-застосунок з JavaScript/HTML5/jQuery</p>
    </div>
</header>

<main style="padding: 20px;">
   
    <div class="toolbar">
        <label>Місяць: </label>
        <select id="selectMonth" style="padding: 6px; margin-right: 15px;">
            <option value="1">Січень</option>
            <option value="2">Лютий</option>
            <option value="3">Березень</option>
            <option value="4">Квітень</option>
            <option value="5">Травень</option>
            <option value="6">Червень</option>
            <option value="7">Липень</option>
            <option value="8">Серпень</option>
            <option value="9">Вересень</option>
            <option value="10">Жовтень</option>
            <option value="11">Листопад</option>
            <option value="12">Грудень</option>
        </select>

        <label>Рік: </label>
        <select id="selectYear" style="padding: 6px; margin-right: 15px;">
            <?php
            $currentYear = date("Y");
            for($i = 2015; $i <= 2030; $i++) {
                $selected = ($i == $currentYear) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
        </select>

        <label>Місткість: </label>
        <select id="selectCapacity" style="padding: 6px; margin-right: 15px;">
            <option value="0">Всі номери</option>
            <option value="1">1 ліжко</option>
            <option value="2">2 ліжка</option>
            <option value="3">3 ліжка</option>
            <option value="4">4+ ліжка</option>
        </select>

        <button id="btnUpdate" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Оновити календар
        </button>
    </div>

    <div id="dp"></div>
</main>

<script type="text/javascript">
    var dp = new DayPilot.Scheduler("dp");

    dp.eventHtmlHandling = "Enabled";
    dp.eventHeight = 50; // Висоти 50 тепер має вистачити, бо шрифт менший
    dp.eventBarMode = "Full";
    dp.allowHtml = true;
    dp.scale = "Day";
    dp.startDate = "<?php echo date('Y-m-01'); ?>";
    dp.days = <?php echo date('t'); ?>;
    dp.cellWidth = 50;
    dp.headerHeight = 40;
    dp.timeHeaders = [
        { "groupBy": "Month", "format": "MMMM yyyy" },
        { "groupBy": "Day", "format": "d" }
    ];

    // Додайте це перед dp.init()
    dp.onBeforeEventRender = function(args) {
        // Якщо в даних є поле html (яке ми додамо в PHP), використовуємо його
        if (args.data.html) {
            args.data.html = args.data.html;
        } else {
            // Якщо ні, беремо поле text (там зараз ваше ім'я + теги)
            args.data.html = args.data.text;
        }
    };

    // Збільште висоту ще трохи для тесту
    dp.eventHeight = 65;
    dp.rowHeaderColumns = [
        { name: "Номер",     width: 160 },
        { name: "Місткість", width: 120 },
        { name: "Статус",    width: 190 }
    ];

    dp.onBeforeRowHeaderRender = function(args) {
        var r = args.row.data;
        if (!r) return;

        args.row.columns[0].html = "<strong>" + r.name + "</strong>";
        args.row.columns[1].html = r.capacity + (r.capacity == 1 ? " ліжко" : " ліжка");

        var status = (r.status || "").toLowerCase();
        var barClass = "bar-Ready";
        var statusText = r.status || "Unknown";

        if (status === "dirty")   { barClass = "bar-Dirty";   statusText = "Dirty"; }
        if (status === "cleanup") { barClass = "bar-Cleanup"; statusText = "Cleanup"; }
        if (status === "ready")   { barClass = "bar-Ready";   statusText = "Ready"; }

        args.row.columns[2].html = '<span class="status-bar ' + barClass + '"></span><strong>' + statusText + '</strong>';
    };

    function loadResources() {
        var capacity = $("#selectCapacity").val() || 0;

        $.ajax({
            url: "backend_rooms.php",
            data: { capacity: capacity },
            dataType: "json",
            success: function(data) {
                dp.resources = data;
                dp.update();
            }
        });
    }


    
    // Оновлена функція з передачею місяця і року
    function loadEvents() {
        var month = $("#selectMonth").val();
        var year  = $("#selectYear").val();

        $.ajax({
            url: "backend_reservations.php",
            data: { 
                month: month,
                year: year 
            },
            dataType: "json",
            success: function(data) {
                dp.events.list = data;   // важливий момент для DayPilot
                dp.update();
            }
        });
    }

    $("#btnUpdate").click(function() {
        var month = $("#selectMonth").val().padStart(2, '0');
        var year  = $("#selectYear").val();

        var startStr = year + "-" + month + "-01";
        var daysInMonth = new Date(year, parseInt(month), 0).getDate();

        dp.startDate = startStr;
        dp.days = daysInMonth;

        dp.update();
        loadResources();
        loadEvents();

        dp.message("Календар оновлено: " + $("#selectMonth option:selected").text() + " " + year);
    });

    dp.onEventClick = function (args) {
        var e = args.e;

        // Створюємо список номерів для вибору (combobox)
        var roomOptions = dp.resources.map(function(item) {
            return { name: item.name, id: item.id };
        });

        var form = [
            {name: 'Прізвище та ім\'я', id: 'name', type: 'text'},
            {name: 'Дата заїзду', id: 'start', type: 'datetime'},
            {name: 'Дата виїзду', id: 'end', type: 'datetime'},
            {name: 'Номер кімнати', id: 'room_id', type: 'select', options: roomOptions},
            {name: 'Статус', id: 'status', type: 'select', options: [
                {name: "Новий (New)", id: "New"},
                {name: "Підтверджено (Confirmed)", id: "Confirmed"},
                {name: "Прибув (Arrived)", id: "Arrived"},
                {name: "Виїхав (CheckedOut)", id: "CheckedOut"},
                {name: "Протерміновано (Expired)", id: "Expired"}
            ]},
            {name: 'Оплата (%)', id: 'paid', type: 'number'}
        ];

        // Заповнюємо форму поточними даними з об'єкта події
        var data = {
            name: e.data.name,      // беремо чисте ім'я
            start: e.start(),       // автоматично підтягує дату
            end: e.end(),           // автоматично підтягує дату
            room_id: e.resource(),  // поточна кімната
            status: e.data.status,
            paid: e.data.paid
        };

        DayPilot.Modal.form(form, data).then(function(modal) {
            if (modal.canceled) return;

            // Логіка кнопок через стандартний confirm (або можна додати власну кнопку у Modal)
            if (confirm("Зберегти зміни? (Натисніть 'Скасувати', якщо хочете ВИДАЛИТИ)")) {
                // ОНОВЛЕННЯ ВСІХ ДАНИХ
                $.post("backend_update.php", {
                    id: e.id(),
                    name: modal.result.name,
                    start: modal.result.start.toString(),
                    end: modal.result.end.toString(),
                    room_id: modal.result.room_id,
                    status: modal.result.status,
                    paid: modal.result.paid,
                    action: 'update'
                }, function() {
                    dp.message("Зміни збережено");
                    loadEvents();
                });
            } else {
                // ВИДАЛЕННЯ
                if (confirm("Ви точно хочете видалити це бронювання?")) {
                    $.post("backend_update.php", {
                        id: e.id(),
                        action: 'delete'
                    }, function() {
                        dp.message("Видалено");
                        loadEvents();
                    });
                }
            }
        });
    };

    dp.onEventMoved = function(args) {
        $.post("backend_move.php", {
            id: args.e.id(),
            newStart: args.newStart.toString(),
            newEnd: args.newEnd.toString(),
            newResource: args.newResource
        }, function(response) {
            if (response.result === "OK") {
                dp.message("Бронювання переміщено");
            } else {
                // Повідомляємо про конфлікт
                DayPilot.Modal.alert("❌ Неможливо перемістити: " + response.message);
                loadEvents(); // Перевантажуємо, щоб повернути блок на старе місце
            }
        }).fail(function() {
            DayPilot.Modal.alert("❌ Сталася помилка на сервері!");
            loadEvents();
        });
    };

    dp.onEventResized = function(args) {
        $.post("backend_move.php", {
            id: args.e.id(),
            newStart: args.newStart.toString(),
            newEnd: args.newEnd.toString(),
            newResource: args.e.resource()
        }, function() {
            dp.message("Тривалість змінено");
            loadEvents();
        });
    };

    dp.onTimeRangeSelected = function (args) {
        // Оновлений повний список статусів для створення
        var statuses = [
            {name: "Новий (New)", id: "New"},
            {name: "Підтверджено (Confirmed)", id: "Confirmed"},
            {name: "Заселено (Arrived)", id: "Arrived"},
            {name: "Перевірено (CheckedOut)", id: "CheckedOut"},
            {name: "Протерміновано (Expired)", id: "Expired"}
        ];

        var form = [
            {name: 'Прізвище та ім\'я', id: 'name', type: 'text', default: 'Новий гість'},
            {name: 'Статус', id: 'status', type: 'select', options: statuses, default: 'New'},
            {name: 'Оплата (%)', id: 'paid', type: 'number', default: 0}
        ];

        DayPilot.Modal.form(form, {}).then(function(modal) {
            dp.clearSelection();
            if (modal.canceled) return;

            $.post("backend_create.php", {
                start: args.start.toString(),
                end: args.end.toString(),
                resource: args.resource,
                name: modal.result.name,
                status: modal.result.status,
                paid: modal.result.paid
            }, function(response) {
                if (response.result === "OK") {
                    dp.message("Бронювання успішно створено!");
                    loadEvents();
                } else {
                    DayPilot.Modal.alert(response.message || "Помилка при створенні");
                }
            });
        });
    };

    // Ініціалізація
    dp.init();
    loadResources();
    loadEvents();
</script>

<footer>
    <address style="padding:20px; color:#95a5a6; border-top:1px solid #eee; text-align:center;">
        (c) Автор лабораторної роботи: студент спеціальності ПЗІС, Якимович Назарій Андрійович
    </address>
</footer>
</body>
</html>