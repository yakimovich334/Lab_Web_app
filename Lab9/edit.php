<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Редагування бронювання</title>
    <link type="text/css" rel="stylesheet" href="css/style.css" />    
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body style="background: #fff; padding: 20px;">
    <?php
        require_once '_db.php'; // Використовуємо твій $db або $pdo
        
        // Отримуємо дані поточної броні
        $stmt = $db->prepare('SELECT * FROM reservations WHERE id = :id');
        $stmt->execute(['id' => $_GET['id']]);
        $res = $stmt->fetch();

        // Отримуємо список кімнат для випадаючого списку
        $rooms = $db->query('SELECT * FROM rooms ORDER BY name');
    ?>

    <form id="f">
        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
        
        <h1>Редагувати бронювання</h1>
        
        <div>Ім'я клієнта:</div>
        <input type="text" name="name" value="<?php echo htmlspecialchars($res['name']); ?>" style="width:100%" />

        <div style="margin-top:10px;">Статус:</div>
        <select name="status" style="width:100%">
            <?php 
                $options = array("New", "Confirmed", "Arrived", "CheckedOut");
                foreach ($options as $opt) {
                    $selected = ($opt == $res['status']) ? 'selected' : '';
                    echo "<option value='$opt' $selected>$opt</option>";
                }
            ?>
        </select>

        <div style="margin-top:10px;">Оплата (%):</div>
        <select name="paid" style="width:100%">
            <?php 
                foreach (array(0, 50, 100) as $p) {
                    $selected = ($p == $res['paid']) ? 'selected' : '';
                    echo "<option value='$p' $selected>$p %</option>";
                }
            ?>
        </select>

        <div style="margin-top:10px;">Кімната:</div>
        <select name="room" style="width:100%">
            <?php 
                foreach ($rooms as $room) {
                    $selected = ($room['id'] == $res['room_id']) ? 'selected' : '';
                    echo "<option value='{$room['id']}' $selected>{$room['name']}</option>";
                }
            ?>
        </select>

        <div style="margin-top:20px;">
            <input type="button" id="save" value="Зберегти" style="background:#2ecc71; color:white; border:none; padding:8px 15px; cursor:pointer;" />
            <input type="button" id="delete" value="Видалити" style="background:#e74c3c; color:white; border:none; padding:8px 15px; cursor:pointer;" />
            <a href="javascript:parent.DayPilot.Modal.close();" style="margin-left:10px;">Скасувати</a>
        </div>
    </form>

    <script>
        // Збереження через AJAX
            $("#save").click(function() {
                var data = $("#f").serialize(); // Збирає всі поля форми
                $.post("backend_update.php", data, function(response) {
                    // parent звертається до головного вікна index.php
                    parent.DayPilot.Modal.close("OK"); 
                });
            });

            // Видалення
            $("#delete").click(function() {
                if (confirm("Видалити це бронювання?")) {
                    var id = $("input[name='id']").val();
                    $.post("backend_delete.php", { id: id }, function(response) {
                        parent.DayPilot.Modal.close("OK");
                    });
                }
            });
    </script>
</body>
</html>