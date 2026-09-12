<?php 
$pageTitle = 'Reception Desk';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Reception Desk</h2>
<div class="card">
    <p>Welcome, <?= esc($me['name']) ?>.</p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
