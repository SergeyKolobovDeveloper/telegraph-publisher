<?php

require_once 'TelegraphPublisher.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $publisher = new TelegraphPublisher();
    
    $author = 'Affnetix Developer';
    $publisher->initAccount('AffnetixBot', $author);

    $title = 'Автоматичний звіт: Тест Telegra.ph API ';
    
    $content = [
        [
            'tag' => 'h3',
            'children' => ['Успішна інтеграція PHP та Telegra.ph API']
        ],
        [
            'tag' => 'p',
            'children' => ['Цей матеріал було створено автоматично за допомогою об\'єктно-орієнтованого PHP-скрипта (використовуючи cURL та роботу з файловим кешуванням).']
        ],
        [
            'tag' => 'p',
            'children' => ['Всі результати публікацій автоматично фіксуються у файл result.csv у форматі Title | Author | URL | Date.']
        ]
    ];

    $result = $publisher->createPage($title, $content, $author);

    if (!empty($result['ok']) && $result['ok'] === true) {
        $pageUrl = $result['result']['url'];
        
        echo "<div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h2 style='color: #2e7d32;'>Статтю успішно опубліковано!</h2>";
        echo "<p><strong>Заголовок:</strong> {$title}</p>";
        echo "<p><strong>Автор:</strong> {$author}</p>";
        echo "<p><strong>Посилання:</strong> <a href='{$pageUrl}' target='_blank'>{$pageUrl}</a></p>";
        echo "<hr style='border: 0; border-top: 1px solid #eee; margin: 15px 0;'>";
        echo "<p style='color: #666; font-size: 14px;'>Дані про публікацію успішно додано до файлу <code>result.csv</code>.</p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>Помилка при публікації статті.</p>";
    }

} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial, sans-serif; padding: 20px;'>";
    echo "<h3>Сталася помилка:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}