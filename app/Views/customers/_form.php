<?php $customer=$customer??[]; $isEdit=!empty($customer['id']); $v=fn($f,$d='')=>old($f,$customer[$f]??$d); ?>
<div class="card form-card">
 <div class="form-grid">
  <div class="form-group"><label>Customer Code *</label><input name="customer_code" value="<?=esc($v('customer_code'))?>" maxlength="30" required placeholder="CUST-0001"></div>
  <div class="form-group"><label>Customer Type *</label><select name="customer_type" required><option value="">-- Select Type --</option><option value="individual" <?=$v('customer_type')==='individual'?'selected':''?>>Individual</option><option value="corporate" <?=$v('customer_type')==='corporate'?'selected':''?>>Corporate</option></select></div>
  <div class="form-group full"><label>Customer Name *</label><input name="name" value="<?=esc($v('name'))?>" maxlength="150" required placeholder="Full customer or company name"></div>
  <div class="form-group"><label>Phone / WhatsApp</label><input name="phone" value="<?=esc($v('phone'))?>" maxlength="30" placeholder="0812..."></div>
  <div class="form-group"><label>Email</label><input type="email" name="email" value="<?=esc($v('email'))?>" maxlength="150"></div>
  <div class="form-group full"><label>Address</label><textarea name="address"><?=esc($v('address'))?></textarea></div>
  <div class="form-group"><label>City</label><input name="city" value="<?=esc($v('city'))?>" maxlength="100"></div>
  <div class="form-group"><label>Province</label><input name="province" value="<?=esc($v('province'))?>" maxlength="100"></div>
  <div class="form-group"><label>Postal Code</label><input name="postal_code" value="<?=esc($v('postal_code'))?>" maxlength="10"></div>
  <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?=$v('is_active',1)?'checked':''?>> Active customer</label></div>
  <div class="form-group full"><label>Notes</label><textarea name="notes"><?=esc($v('notes'))?></textarea></div>
 </div>
 <div class="form-actions"><a class="btn btn-secondary" href="<?=site_url('customers')?>">Cancel</a><button class="btn btn-primary" type="submit"><?=$isEdit?'Save Changes':'Create Customer'?></button></div>
</div>
