<?php $p = $passenger ?? []; $v = fn($f,$d='')=> old($f,$p[$f]??$d); ?>
<div class="card form-card">
    <div class="form-grid">
        <div class="form-group"><label>Passenger Code</label><input name="passenger_code" value="<?= esc($v('passenger_code')) ?>" placeholder="Optional"></div>
        <div class="form-group"><label>Full Name *</label><input name="full_name" required value="<?= esc($v('full_name')) ?>"></div>
        <div class="form-group"><label>Gender</label><select name="gender"><option value="">--</option><option value="M" <?= $v('gender')=='M'?'selected':'' ?>>Male</option><option value="F" <?= $v('gender')=='F'?'selected':'' ?>>Female</option></select></div>
        <div class="form-group"><label>Birth Date</label><input type="date" name="birth_date" value="<?= esc($v('birth_date')) ?>"></div>
        <div class="form-group"><label>Passport No</label><input name="passport_no" value="<?= esc($v('passport_no')) ?>"></div>
        <div class="form-group"><label>Phone</label><input name="phone" value="<?= esc($v('phone')) ?>"></div>
        <div class="form-group"><label>Email</label><input name="email" type="email" value="<?= esc($v('email')) ?>"></div>
    </div>
</div>
