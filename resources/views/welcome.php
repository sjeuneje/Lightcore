<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightcore</title>
</head>
<body>
    <h1>A New Lightcore Project</h1>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <h2><?php echo $task->content; ?></h2>
        <?php endforeach; ?>
    </ul>
</body>
</html>
<?php /** @var $tasks */ ?>
