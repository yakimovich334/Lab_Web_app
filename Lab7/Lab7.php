<?php
// 1. Початок сесії для збереження стану ігрового поля
session_start();

// 2. Функція для ініціалізації або скидання гри
function resetGame() {
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['player'] = 'X';
    $_SESSION['winner'] = null;
    $_SESSION['is_draw'] = false;
}

// Якщо натиснуто кнопку "Почати заново" або гра ще не створена
if (isset($_POST['reset']) || !isset($_SESSION['board'])) {
    resetGame();
}

// 3. Обробка ходу гравця
if (isset($_POST['cell']) && $_SESSION['winner'] === null) {
    $index = $_POST['cell'];
    
    // Перевіряємо, чи клітинка порожня
    if ($_SESSION['board'][$index] === '') {
        $_SESSION['board'][$index] = $_SESSION['player'];
        
        // Перевірка на перемогу
        $win_patterns = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Горизонталі
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Вертикалі
            [0, 4, 8], [2, 4, 6]             // Діагоналі
        ];

        foreach ($win_patterns as $pattern) {
            if ($_SESSION['board'][$pattern[0]] !== '' &&
                $_SESSION['board'][$pattern[0]] === $_SESSION['board'][$pattern[1]] &&
                $_SESSION['board'][$pattern[1]] === $_SESSION['board'][$pattern[2]]) {
                $_SESSION['winner'] = $_SESSION['board'][$pattern[0]];
                break;
            }
        }

        // Перевірка на нічию (якщо немає переможця і не залишилось порожніх клітинок)
        if (!$_SESSION['winner'] && !in_array('', $_SESSION['board'])) {
            $_SESSION['is_draw'] = true;
        }

        // Зміна гравця, якщо гра триває
        if (!$_SESSION['winner'] && !$_SESSION['is_draw']) {
            $_SESSION['player'] = ($_SESSION['player'] === 'X') ? 'O' : 'X';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Хрестики-Нолики на PHP</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; background: #f4f4f9; }
        h1 { color: #333; }
        .status { margin-bottom: 20px; font-size: 1.2em; font-weight: bold; }
        .board { 
            display: grid; 
            grid-template-columns: repeat(3, 100px); 
            grid-gap: 5px; 
            background: #444; 
            padding: 5px; 
            border-radius: 8px;
        }
        .cell {
            width: 100px;
            height: 100px;
            background: #fff;
            border: none;
            font-size: 2em;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .cell:hover { background: #eee; }
        .cell:disabled { cursor: default; color: #555; }
        .reset-btn { margin-top: 20px; padding: 10px 20px; font-size: 1em; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 5px; }
        .winner { color: #d9534f; }
    </style>
</head>
<body>

    <h1>Хрестики-Нолики</h1>

    <div class="status">
        <?php if ($_SESSION['winner']): ?>
            <span class="winner">Переміг гравець: <?php echo $_SESSION['winner']; ?>!</span>
        <?php elseif ($_SESSION['is_draw']): ?>
            <span>Нічия!</span>
        <?php else: ?>
            Зараз ходить: <?php echo $_SESSION['player']; ?>
        <?php endif; ?>
    </div>

    <form method="post">
        <div class="board">
            <?php foreach ($_SESSION['board'] as $index => $value): ?>
                <button type="submit" name="cell" value="<?php echo $index; ?>" class="cell" 
                    <?php echo ($value !== '' || $_SESSION['winner'] !== null) ? 'disabled' : ''; ?>>
                    <?php echo $value; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <center>
            <button type="submit" name="reset" class="reset-btn">Почати заново</button>
        </center>
    </form>

</body>
</html>