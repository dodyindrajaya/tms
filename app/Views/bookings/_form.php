<?php
$b=$booking??[]; $item=$item??[];
$v=fn($f,$d='')=>old($f,$b[$f]??$d);
$iv=fn($f,$d='')=>old($f,$item[$f]??$d);
?>
<style>
/* Form UX improvements for booking entry */
.booking-form-card { display:flex; flex-direction:column; gap:14px; }
.form-section-title { font-weight:800; color:var(--primary-dark); font-size:13px; }
.section-hint { color:var(--muted); font-size:12px; margin-left:8px; }
.booking-commercial-grid { display:flex; gap:18px; align-items:flex-start; }
.booking-item-form { flex:1; }
.booking-summary-card { width:320px; flex:0 0 320px; border-left:1px solid var(--border); padding-left:16px; }
@media (max-width:900px){ .booking-commercial-grid{flex-direction:column} .booking-summary-card{width:100%;flex:unset;border-left:0;padding-left:0} }
.booking-summary-title{ font-size:11px; color:var(--muted); font-weight:800; letter-spacing:.8px}
.booking-summary-table td{font-size:13px}
.booking-summary-table tfoot th{font-size:18px;color:var(--primary);}
.booking-summary-total { font-size:20px; font-weight:900; color:var(--primary-dark); }
.btn-primary.full { width:220px; }
@media (max-width:600px){ .btn-primary.full{ width:100%; } }
</style>
<div class="card form-card booking-form-card">
<div class="form-section-title">Booking Information</div>

<div class="form-grid">
<div class="form-group"><label>Customer *</label><select id="customer_id" name="customer_id" required class="select-search"><option value="">-- Select Customer --</option><?php foreach($customers as $c): ?><option value="<?=$c['id']?>" <?=$v('customer_id')==$c['id']?'selected':''?>><?=esc($c['name'])?> (<?=esc($c['customer_code'])?>)</option><?php endforeach; ?></select></div>
<div class="form-group"><label>Booking Date *</label><input type="datetime-local" name="booking_date" value="<?=esc(str_replace(' ','T',substr($v('booking_date',date('Y-m-d H:i:s')),0,16)))?>" required></div>
<div class="form-group"><label>Travel Start</label><input type="date" name="travel_start_date" value="<?=esc($v('travel_start_date'))?>"></div>
<div class="form-group"><label>Travel End</label><input type="date" name="travel_end_date" value="<?=esc($v('travel_end_date'))?>"></div>
<div class="form-group"><label>Source *</label><select name="source" required><?php foreach($sources as $k=>$label): ?><option value="<?=$k?>" <?=$v('source','walk_in')===$k?'selected':''?>><?=$label?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Status *</label><select name="status" required><?php foreach($statuses as $k=>$label): ?><option value="<?=$k?>" <?=$v('status','draft')===$k?'selected':''?>><?=$label?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Currency *</label><input name="currency_code" value="<?=esc($v('currency_code','IDR'))?>" maxlength="3" required></div>
<div class="form-group full"><label>Notes</label><textarea name="notes" placeholder="Add notes or internal info..."><?=esc($v('notes'))?></textarea></div>
</div>

<div class="form-section-title">First Booking Item <span class="section-hint">MVP supports one line here; additional lines will be added next.</span></div>

<div class="booking-commercial-grid booking-commercial-grid-v4">
<div class="booking-item-form booking-item-form-v4">
<div class="form-group"><label>Product *</label><select id="product_id" name="product_id" required class="select-search"><option value="">-- Select Product --</option><?php foreach($products as $p): ?><option value="<?=$p['id']?>" data-price="<?=$p['default_sale_price']?>" <?=$iv('product_id')==$p['id']?'selected':''?>><?=esc($p['name'])?> — <?=esc($p['product_code'])?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Description</label><input name="description" value="<?=esc($iv('description'))?>"></div>

<?php
$initialQty=(float)$iv('quantity',1);
$initialUnit=(float)$iv('unit_sale_price',0);
$initialDiscount=(float)$iv('discount_amount',0);
$initialTaxAmount=(float)$iv('tax_amount',0);
$initialTaxBase=max(0,($initialQty*$initialUnit)-$initialDiscount);
$initialTaxPercent=$initialTaxBase>0?round(($initialTaxAmount/$initialTaxBase)*100,4):0;
?>

<div class="booking-calculation-fields booking-calculation-fields-v4">
<div class="form-group"><label>Quantity *</label><input type="number" step="0.01" min="0.01" name="quantity" id="quantity" value="<?=esc($initialQty)?>" required></div>
<div class="form-group"><label>Unit Sale Price *</label><input type="number" step="0.01" min="0" name="unit_sale_price" id="unit_sale_price" value="<?=esc($initialUnit)?>" required></div>
<div class="form-group"><label>Discount</label><input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" value="<?=esc($initialDiscount)?>"></div>
<div class="form-group"><label>Tax (%)</label><div class="input-suffix-wrap tax-input-wrap"><input type="number" step="0.01" min="0" max="100" name="tax_percent" id="tax_percent" value="<?=esc($initialTaxPercent)?>"><span>%</span></div><small class="field-help">Enter the tax percentage, e.g. 10.</small></div>
</div>
</div>

<aside class="booking-summary-card booking-summary-card-v4">
    <br>
<div class="booking-summary-title">BOOKING SUMMARY</div>
<table class="booking-summary-table booking-summary-table-v4">
<tbody>
<tr><td>Subtotal</td><td id="summary_subtotal">Rp 0</td></tr>
<tr><td>Discount</td><td id="summary_discount">Rp 0</td></tr>
<tr><td>Tax</td><td id="summary_tax_percent">0%</td></tr>
<tr><td>Tax Amount</td><td id="summary_tax_amount">Rp 0</td></tr>
</tbody>
<tfoot><tr><th class="bold">TOTAL</th><th id="summary_total" class="booking-summary-total">Rp 0</th></tr></tfoot>
</table>
</aside>
</div>

<div class="form-actions"><a class="btn btn-secondary" href="<?=site_url('bookings')?>">Cancel</a><button class="btn btn-primary full"><?=!empty($b['id'])?'Save Changes':'Create Booking'?></button></div>
</div>

<script>
(function(){
const product=document.getElementById('product_id'),qty=document.getElementById('quantity'),unit=document.getElementById('unit_sale_price'),discount=document.getElementById('discount_amount'),tax=document.getElementById('tax_percent');
const subtotal=document.getElementById('summary_subtotal'),discountEl=document.getElementById('summary_discount'),taxEl=document.getElementById('summary_tax_percent'),taxAmount=document.getElementById('summary_tax_amount'),total=document.getElementById('summary_total');
const money=v=>'Rp '+new Intl.NumberFormat('id-ID',{maximumFractionDigits:2,minimumFractionDigits:0}).format(v||0);
function calc(){
const q=parseFloat(qty?.value)||0,u=parseFloat(unit?.value)||0,d=Math.max(0,parseFloat(discount?.value)||0),t=Math.max(0,Math.min(100,parseFloat(tax?.value)||0));
const gross=Math.max(0,q*u),disc=Math.min(d,gross),base=Math.max(0,gross-disc),taxAmt=Math.round(base*t)/100,totalAmt=Math.round((base+taxAmt)*100)/100;
subtotal.textContent=money(gross);discountEl.textContent=disc>0?'- '+money(disc):money(0);taxEl.textContent=t+'%';taxAmount.textContent=money(taxAmt);total.textContent=money(totalAmt);
}
product?.addEventListener('change',function(){const p=this.options[this.selectedIndex]?.dataset.price;if(p&&(!unit.value||+unit.value===0))unit.value=p;calc();});
[qty,unit,discount,tax].forEach(e=>{e?.addEventListener('input',calc);e?.addEventListener('change',calc);});calc();
})();
</script>

<!-- Choices.js (searchable select, no jQuery) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        new Choices('#customer_id', {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            placeholderValue: 'Search customer...'
        });

        new Choices('#product_id', {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            placeholderValue: 'Search product...'
        });
    } catch(e) {
        console.warn('Choices init failed', e);
    }

    // Ensure product change still fills price into unit field
    var prod = document.getElementById('product_id');
    if (prod) {
        prod.addEventListener('change', function(){
            var opt = this.options[this.selectedIndex];
            if (opt && opt.dataset && opt.dataset.price) {
                var unit = document.getElementById('unit_sale_price');
                if (unit && (!unit.value || +unit.value === 0)) unit.value = opt.dataset.price;
            }
            // trigger existing calc
            var ev = new Event('input', {bubbles:true});
            prod.dispatchEvent(ev);
        });
    }
});
</script>
