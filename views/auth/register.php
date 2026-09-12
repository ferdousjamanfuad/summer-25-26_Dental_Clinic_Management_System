<?php 
$pageTitle = 'Sign Up';
require __DIR__ . '/../partials/header.php'; 
?>

<div class="card" style="max-width: 480px; margin: 30px auto;">
    <h2>Patient / Staff Registration</h2>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">Register as a Patient, Doctor, or Receptionist.</p>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-error"><?= esc($error) ?></div>
    <?php endif; ?>

    <form action="index.php?page=register" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required value="<?= esc($_POST['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required value="<?= esc($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Phone</label>
            <input type="text" name="contact" class="form-control" required value="<?= esc($_POST['contact'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required value="<?= esc($_POST['username'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Select Role</label>
            <select name="role" class="form-control" required>
                <option value="patient">Patient</option>
                <option value="doctor">Doctor (Requires Admin Approval)</option>
                <option value="receptionist">Receptionist (Requires Admin Approval)</option>
            </select>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Create Account</button>
    </form>

    <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
        Already registered? <a href="index.php?page=login" style="color: #0077b6; font-weight: bold;">Login here</a>
    </p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
