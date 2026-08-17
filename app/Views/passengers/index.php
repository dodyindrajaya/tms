<?php
$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
?>
<div class="tms-page">

<div class="tms-page-head"><div><div class="eyebrow">MASTER DATA / PASSENGERS</div><h1>Passengers</h1><p>Passenger master used by bookings and ticketing.</p></div><a class="tms-btn tms-btn-primary" href="<?=site_url('passengers/new')?>">+ New Passenger</a></div>
<?php if($flashSuccess):?><div class="tms-alert tms-alert-success"><?=esc($flashSuccess)?></div><?php endif;?><?php if($flashError):?><div class="tms-alert tms-alert-danger"><?=esc($flashError)?></div><?php endif;?>
<form class="tms-filter" method="get"><input name="q" value="<?=esc($q)?>" placeholder="Search passenger code, name, phone, email or passport..."><button class="tms-btn tms-btn-secondary">Search</button></form>
<div class="tms-card"><div class="tms-card-head"><div><h2>Passenger Records</h2><p><?=count($passengers)?> record(s)</p></div></div><div class="tms-table-wrap"><table class="tms-table"><thead><tr><th>CODE</th><th>PASSENGER</th><th>CUSTOMER</th><th>CONTACT</th><th>PASSPORT / ID</th><th>ACTION</th></tr></thead><tbody>
<?php if(!$passengers):?><tr><td colspan="6" class="empty">No passenger records found.</td></tr><?php endif;?>
<?php foreach($passengers as $p):?><tr><td><strong><?=esc($p['passenger_code'])?></strong></td><td><strong><?=esc($p['full_name'])?></strong><small><?=esc($p['gender']?:'-')?> · <?=esc($p['birth_date']?:'-')?></small></td><td><?=esc($p['customer_name']?:'-')?></td><td><?=esc($p['phone']?:'-')?><small><?=esc($p['email']?:'-')?></small></td><td><?=esc($p['passport_no']?:'-')?><small><?=esc($p['id_number']?:'-')?></small></td><td class="actions"><a class="tms-btn tms-btn-sm" href="<?=site_url('passengers/show/'.$p['id'])?>">View</a><a class="tms-btn tms-btn-sm" href="<?=site_url('passengers/edit/'.$p['id'])?>">Edit</a></td></tr><?php endforeach;?>
</tbody></table></div></div>

</div>
<style>
.tms-page{max-width:1200px;margin:0 auto;padding:28px 24px 50px;color:#123247}
.tms-page-head{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;margin-bottom:22px}
.tms-page-head h1{margin:4px 0;font-size:28px;line-height:1.15;color:#0d2638}.tms-page-head p{margin:0;color:#718493;font-size:13px}
.eyebrow{font-size:10px;font-weight:800;letter-spacing:.08em;color:#078dc4}.head-actions{display:flex;gap:8px}
.tms-card{background:#fff;border:1px solid #dbe6ed;border-radius:12px;box-shadow:0 5px 20px rgba(16,45,60,.05);margin-bottom:18px;overflow:hidden}
.tms-card-head{display:flex;justify-content:space-between;align-items:center;padding:16px 18px;border-bottom:1px solid #e6edf1}
.tms-card-head h2{font-size:15px;margin:0 0 3px;color:#17364a}.tms-card-head p{font-size:11px;color:#81919d;margin:0}
.tms-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 13px;border:1px solid #d5e0e6;border-radius:8px;background:#fff;color:#17364a;text-decoration:none;font-size:12px;font-weight:700;cursor:pointer}
.tms-btn-primary{background:#0aa4d5;border-color:#0aa4d5;color:#fff}.tms-btn-secondary{background:#f7fafc}.tms-btn-sm{padding:6px 9px;font-size:11px}
.tms-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.tms-stat{background:#fff;border:1px solid #dbe6ed;border-radius:11px;padding:15px 16px;box-shadow:0 4px 16px rgba(16,45,60,.04)}
.tms-stat span{font-size:11px;color:#718493;display:block}.tms-stat strong{display:block;font-size:22px;color:#123247;margin:3px 0}.tms-stat small{font-size:10px;color:#99a6ae}
.tms-filter{display:grid;grid-template-columns:minmax(0,1fr) 170px 170px auto;gap:8px;margin-bottom:16px}
.tms-filter input,.tms-filter select,.field input,.field select,.field textarea{width:100%;box-sizing:border-box;border:1px solid #d7e2e8;border-radius:7px;background:#fff;padding:9px 10px;font:inherit;font-size:12px;color:#18364a}
.tms-table-wrap{overflow:auto}.tms-table{width:100%;border-collapse:collapse;min-width:980px}.tms-table th{background:#f5f9fb;color:#587081;font-size:9px;letter-spacing:.05em;text-align:left;padding:11px 12px;border-bottom:1px solid #dfe8ed}
.tms-table td{padding:12px;border-bottom:1px solid #edf2f5;font-size:11px;color:#29485a;vertical-align:middle}.tms-table td strong{display:block;color:#15364b}.tms-table td small{display:block;color:#8a99a3;font-size:10px;margin-top:2px}.empty{text-align:center!important;color:#8a99a3!important;padding:35px!important}.actions{white-space:nowrap}
.type-pill,.status-pill{display:inline-flex;padding:4px 8px;border-radius:99px;font-size:10px;font-weight:700}.type-pill{background:#eef8fc;color:#087ea8}
.status-pill{background:#eef2f5;color:#5d6e78}.status-issued,.status-paid,.status-completed{background:#e8f7ee;color:#13834d}.status-booked,.status-quoted{background:#eaf5fc;color:#087ea8}.status-request{background:#fff5df;color:#a66b00}.status-cancelled,.status-void,.status-refunded{background:#fdebed;color:#bb3743}
.tms-alert{padding:11px 13px;border-radius:8px;margin-bottom:16px;font-size:12px}.tms-alert-success{background:#eaf8f0;color:#157044}.tms-alert-danger{background:#fff0f1;color:#ad303d}
.tms-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px;padding:18px}.field label{display:block;font-size:10px;font-weight:800;color:#4f6777;margin-bottom:5px}.field-full{grid-column:1/-1}
.tms-form-actions{display:flex;justify-content:flex-end;gap:8px;margin:6px 0 20px}
.segment-row{display:grid;grid-template-columns:34px 1fr 32px;gap:10px;padding:16px 18px;border-bottom:1px solid #edf2f5;align-items:start}
.segment-number{width:28px;height:28px;border-radius:50%;background:#eaf7fc;color:#0789b8;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800}
.segment-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px}.remove-segment{width:28px;height:28px;border:1px solid #e7c5c8;border-radius:7px;background:#fff;color:#c03b46;font-size:18px;cursor:pointer}
.tms-detail-hero{display:grid;grid-template-columns:1.2fr 1.2fr .8fr .9fr;gap:1px;background:#dce7ed;border:1px solid #dce7ed;border-radius:12px;overflow:hidden;margin-bottom:18px}
.tms-detail-hero>div{background:#fff;padding:17px}.tms-detail-hero span{display:block;font-size:9px;color:#81919d;font-weight:800}.tms-detail-hero strong{display:block;font-size:14px;margin:6px 0 2px;color:#17364a}.tms-detail-hero small{font-size:10px;color:#8798a3}
.detail-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#e7eef2}.detail-grid>div{background:#fff;padding:14px 16px}.detail-grid span{display:block;color:#8798a3;font-size:9px;text-transform:uppercase}.detail-grid strong{display:block;color:#19394c;font-size:12px;margin-top:4px}
.timeline{padding:6px 18px}.timeline-row{display:grid;grid-template-columns:34px 1fr;gap:12px;padding:15px 0;border-bottom:1px solid #edf2f5}.timeline-no{width:28px;height:28px;border-radius:50%;background:#0aa4d5;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800}
.route-line{display:flex;gap:12px;align-items:center;font-size:14px}.route-line span{color:#8ba0ac}.segment-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:7px}.segment-meta span{font-size:10px;color:#607684;background:#f4f8fa;padding:5px 7px;border-radius:5px}
@media(max-width:900px){.tms-stats{grid-template-columns:repeat(2,1fr)}.tms-filter{grid-template-columns:1fr 1fr}.tms-page-head{flex-direction:column}.segment-fields{grid-template-columns:repeat(2,1fr)}.tms-detail-hero{grid-template-columns:1fr 1fr}.detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.tms-stats,.tms-form-grid,.segment-fields,.tms-detail-hero,.detail-grid{grid-template-columns:1fr}.tms-filter{grid-template-columns:1fr}.field-full{grid-column:auto}}
</style>

