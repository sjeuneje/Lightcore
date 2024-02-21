<h1>A New Lightcore Project</h1>
<ul>
    <?php foreach ($tasks as $task): ?>
        <h2><?php echo $task->content; ?></h2>
    <?php endforeach; ?>
</ul>