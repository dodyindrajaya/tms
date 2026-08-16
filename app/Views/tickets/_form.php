<?php $t=$ticket??[]; $v=fn($f,$d='')=>old($f,$t[$f]??$d); ?>
<div class="card form-card">
<div class="form-section-title">Ticket Booking</div><div class="form-grid">
<div class="form-group"><label>Booking *</label><select name="booking_id" required><option value="">-- Select Booking --</option><?php foreach($bookings as $b): ?><option value="<?=$b['id']?>" <?=$v('booking_id')==$b['id']?'selected':''?>><?=esc($b['booking_no'])?> — <?=number_format((float)$b['total_amount'],0,',','.')?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Passenger *</label><select name="passenger_id" required><option value="">-- Select Passenger --</option><?php foreach($passengers as $p): ?><option value="<?=$p['id']?>" <?=$v('passenger_id')==$p['id']?'selected':''?>><?=esc($p['name'])?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Ticket Type *</label><select name="ticket_type" required><?php foreach($types as $k=>$x): ?><option value="<?=$k?>" <?=$v('ticket_type','flight')===$k?'selected':''?>><?=$x?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Supplier</label><select name="supplier_id"><option value="">-- None --</option><?php foreach($suppliers as $s): ?><option value="<?=$s['id']?>" <?=$v('supplier_id')==$s['id']?'selected':''?>><?=esc($s['name'])?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Booking Code / PNR</label><input name="booking_code" value="<?=esc($v('booking_code'))?>"></div>
<div class="form-group"><label>Ticket Number</label><input name="ticket_number" value="<?=esc($v('ticket_number'))?>"></div>
<div class="form-group"><label>Status *</label><select name="status"><?php foreach($statuses as $k=>$x): ?><option value="<?=$k?>" <?=$v('status','request')===$k?'selected':''?>><?=$x?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Carrier</label><input name="carrier" value="<?=esc($v('carrier'))?>" placeholder="Garuda Indonesia / KAI"></div>
<div class="form-group"><label>Origin</label><input name="origin" value="<?=esc($v('origin'))?>" placeholder="CGK"></div>
<div class="form-group"><label>Destination</label><input name="destination" value="<?=esc($v('destination'))?>" placeholder="DPS"></div>
<div class="form-group"><label>Departure Date</label><input type="date" name="departure_date" value="<?=esc($v('departure_date'))?>"></div>
<div class="form-group"><label>Departure Time</label><input type="time" name="departure_time" value="<?=esc($v('departure_time'))?>"></div>
<div class="form-group"><label>Arrival Date</label><input type="date" name="arrival_date" value="<?=esc($v('arrival_date'))?>"></div>
<div class="form-group"><label>Arrival Time</label><input type="time" name="arrival_time" value="<?=esc($v('arrival_time'))?>"></div>
<div class="form-group"><label>Travel Class</label><input name="travel_class" value="<?=esc($v('travel_class'))?>" placeholder="Economy / Business"></div>
<div class="form-group"><label>Seat</label><input name="seat" value="<?=esc($v('seat'))?>"></div>
<div class="form-group"><label>Issue Date</label><input type="date" name="issue_date" value="<?=esc($v('issue_date'))?>"></div>
<div class="form-group"><label>Cost Price *</label><input type="number" step="0.01" min="0" name="cost_price" value="<?=esc($v('cost_price',0))?>" required></div>
<div class="form-group"><label>Selling Price *</label><input type="number" step="0.01" min="0" name="selling_price" value="<?=esc($v('selling_price',0))?>" required></div>
</div>
<div class="form-actions"><a class="btn btn-secondary" href="<?=site_url('tickets')?>">Cancel</a><button class="btn btn-primary"><?=!empty($t['id'])?'Save Changes':'Create Ticket'?></button></div>
</div>
