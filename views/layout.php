


<?php foreach ($scriptsPorVista[$nombreVista] ?? [] as $script): ?>
    <script
        src="/clinica/assets/js/<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?>"
        defer
    ></script>
<?php endforeach; ?>

</body>
</html>
