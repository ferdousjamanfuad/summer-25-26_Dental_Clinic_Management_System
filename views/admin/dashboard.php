<?php 
$pageTitle = 'Admin Dashboard';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Admin Dashboard</h2>

<div class="nav-tabs">
    <a href="index.php?page=admin&action=users" class="<?= $action === 'users' ? 'active' : '' ?>">User Accounts</a>
    <a href="index.php?page=admin&action=equipment" class="<?= $action === 'equipment' ? 'active' : '' ?>">Clinic Equipment</a>
    <a href="index.php?page=admin&action=report" class="<?= $action === 'report' ? 'active' : '' ?>">Monthly Reports</a>
    <a href="index.php?page=admin&action=doctor_availability" class="<?= $action === 'doctor_availability' ? 'active' : '' ?>">Doctor Schedules</a>
</div>

<?php if(!empty($error)): ?>
    <div class="alert alert-error"><?= esc($error) ?></div>
<?php endif; ?>

<!-- ========================================== -->
<!-- CRUD: SYSTEM USERS                         -->
<!-- ========================================== -->
<?php if($action === 'users'): ?>
    
    <div class="card">
        <h3>Add User Directly</h3>
        <form action="index.php?page=admin&action=add_user" method="POST">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
                <input type="text" name="contact" class="form-control" placeholder="Phone" required>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <select name="role" class="form-control">
                    <option value="doctor">Doctor</option>
                    <option value="receptionist">Receptionist</option>
                    <option value="patient">Patient</option>
                </select>
                <button type="submit" class="btn">Add User</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>All Registered Users</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?= esc($u['name']) ?></td>
                <td><span style="font-weight: bold;"><?= esc(role_label($u['role'])) ?></span></td>
                <td><?= esc($u['email']) ?></td>
                <td><?= esc($u['contact']) ?></td>
                <td>
                    <?php if($u['status'] === 'pending'): ?>
                        <span style="color:orange; font-weight:bold;">Pending Approval</span>
                    <?php elseif($u['status'] === 'active'): ?>
                        <span style="color:green; font-weight:bold;">Active</span>
                    <?php else: ?>
                        <span style="color:red; font-weight:bold;">Suspended</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($u['id'] !== $me['id']): ?>
                        <?php if($u['status'] !== 'active'): ?>
                            <a href="<?= csrf_url('index.php?page=admin&action=status&status=active&id='.$u['id']) ?>" class="btn btn-success" style="font-size: 0.8rem; padding: 4px 8px;">Approve</a>
                        <?php endif; ?>
                        <?php if($u['status'] !== 'suspended'): ?>
                            <a href="<?= csrf_url('index.php?page=admin&action=status&status=suspended&id='.$u['id']) ?>" class="btn btn-danger" style="font-size: 0.8rem; padding: 4px 8px;">Suspend</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <i>Current Admin</i>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

<!-- ========================================== -->
<!-- FEATURE 1: EQUIPMENT CHECK                 -->
<!-- ========================================== -->
<?php elseif($action === 'equipment'): ?>
    
    <div class="card">
        <h3>Add Clinic Equipment</h3>
        <form action="index.php?page=admin&action=add_equipment" method="POST">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="name" class="form-control" placeholder="Equipment Name (e.g. Dental X-Ray)" required>
                <input type="text" name="description" class="form-control" placeholder="Description/Model">
                <input type="number" name="quantity" class="form-control" placeholder="Qty" value="1" min="1" required style="width: 100px;">
                <button type="submit" class="btn">Add Equipment</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Equipment Inventory & Status</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Equipment Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Assigned Doctor</th>
            </tr>
            <?php foreach($equipments as $eq): ?>
            <tr>
                <td><?= esc($eq['id']) ?></td>
                <td><strong><?= esc($eq['name']) ?></strong></td>
                <td><?= esc($eq['description'] ?: 'N/A') ?></td>
                <td><?= esc($eq['quantity']) ?></td>
                <td>
                    <?php if($eq['status'] === 'available'): ?>
                        <span style="color: green; font-weight: bold;">Available</span>
                    <?php elseif($eq['status'] === 'in_use'): ?>
                        <span style="color: blue; font-weight: bold;">In Use</span>
                    <?php else: ?>
                        <span style="color: red; font-weight: bold;">Maintenance</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($eq['doctor_name'] ?: 'None (Unassigned)') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

<!-- ========================================== -->
<!-- FEATURE 2: MONTHLY REPORT & MONITOR        -->
<!-- ========================================== -->
<?php elseif($action === 'report'): ?>
    
    <div class="card">
        <h3>Monthly Clinic Overview & Statistics</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <div style="background: #eef8fc; padding: 20px; border-radius: 8px; border-left: 4px solid #0077b6;">
                <h4 style="margin: 0; color: #666;">Total Registered Users</h4>
                <h2 style="margin: 10px 0 0; color: #0077b6;"><?= $stats['total_users'] ?></h2>
            </div>
            <div style="background: #eef8fc; padding: 20px; border-radius: 8px; border-left: 4px solid #2ec4b6;">
                <h4 style="margin: 0; color: #666;">Total Doctors</h4>
                <h2 style="margin: 10px 0 0; color: #2ec4b6;"><?= $stats['total_doctors'] ?></h2>
            </div>
            <div style="background: #eef8fc; padding: 20px; border-radius: 8px; border-left: 4px solid #ff9f1c;">
                <h4 style="margin: 0; color: #666;">Total Patients</h4>
                <h2 style="margin: 10px 0 0; color: #ff9f1c;"><?= $stats['total_patients'] ?></h2>
            </div>
            <div style="background: #eef8fc; padding: 20px; border-radius: 8px; border-left: 4px solid #e71d36;">
                <h4 style="margin: 0; color: #666;">Pending User Approvals</h4>
                <h2 style="margin: 10px 0 0; color: #e71d36;"><?= $stats['pending_approvals'] ?></h2>
            </div>
        </div>
    </div>

<!-- ========================================== -->
<!-- FEATURE 3: DOCTOR AVAILABILITY SET         -->
<!-- ========================================== -->
<?php elseif($action === 'doctor_availability'): ?>
    
    <div class="card">
        <h3>Set Doctor Availability / Shift</h3>
        <form action="index.php?page=admin&action=set_schedule" method="POST">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label>Select Doctor</label>
                    <select name="doctor_id" class="form-control" required>
                        <option value="">-- Choose Doctor --</option>
                        <?php foreach($doctors as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?> (<?= esc($d['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label>Day of Week</label>
                    <select name="day_of_week" class="form-control" required>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required value="09:00">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" required value="17:00">
                </div>
                <div style="align-self: flex-end;">
                    <button type="submit" class="btn">Save Schedule</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Weekly Doctor Schedules</h3>
        <table>
            <tr>
                <th>Doctor Name</th>
                <th>Day</th>
                <th>Visiting Hours</th>
                <th>Action</th>
            </tr>
            <?php if(empty($schedules)): ?>
                <tr><td colspan="4" style="text-align: center; color: #888;">No schedules added yet.</td></tr>
            <?php else: ?>
                <?php foreach($schedules as $s): ?>
                <tr>
                    <td><strong><?= esc($s['doctor_name']) ?></strong></td>
                    <td><?= esc($s['day_of_week']) ?></td>
                    <td><?= date('h:i A', strtotime($s['start_time'])) ?> - <?= date('h:i A', strtotime($s['end_time'])) ?></td>
                    <td>
                        <a href="<?= csrf_url('index.php?page=admin&action=delete_schedule&id='.$s['id']) ?>" class="btn btn-danger" style="font-size: 0.8rem; padding: 4px 8px;" onclick="return confirm('Remove this schedule?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
