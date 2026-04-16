<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Новий номер</title>
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body style="background: #fff; padding: 20px;">
    <form id="fRoom" action="backend_room_create.php" method="post">
        <h1>Додати новий номер</h1>
        
        <div>Назва номера (напр. Room 10):</div>
        <input type="text" name="name" style="width:100%" required />

        <div style="margin-top:10px;">Місткість (ліжок):</div>
        <select name="capacity" style="width:100%">
            <option value="1">1 ліжко</option>
            <option value="2">2 ліжка</option>
            <option value="3">3 ліжка</option>
            <option value="4">4+ ліжка</option>
        </select>

        <div style="margin-top:10px;">Статус:</div>
        <select name="status" style="width:100%">
            <option value="Ready">Ready (Готова)</option>
            <option value="Cleanup">Cleanup (Прибирання)</option>
            <option value="Dirty">Dirty (Брудна)</option>
        </select>

        <div style="margin-top:20px;">
            <input type="submit" value="Зберегти" style="background:#2ecc71; color:white; border:none; padding:8px 15px; cursor:pointer;" />
            <a href="javascript:parent.DayPilot.Modal.close();" style="margin-left:10px;">Скасувати</a>
        </div>
    </form>

    <script>
        $("#fRoom").submit(function(e) {
            e.preventDefault();
            var f = $(this);
            $.post(f.attr("action"), f.serialize(), function(response) {
                parent.DayPilot.Modal.close("OK");
            });
        });
    </script>
</body>
</html>