<?php 
$pageTitle = 'Login';
require __DIR__ . '/../partials/header.php'; 
?>

<div class="card" style="max-width: 400px; margin: 40px auto;">
    <h2>Login to Clinic</h2>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error"><?= esc($error) ?></div>
    <?php endif; ?>

    <form action="index.php?page=login" method="POST">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required value="<?= esc($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Sign In</button>
    </form>

    <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
        Don't have an account? <a href="index.php?page=register" style="color: #0077b6; font-weight: bold;">Sign Up as Patient/Staff</a>
    </p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
