<?= $this->extend('layouts/main') ?><?= $this->section('content') ?>
<?= view('components/page_header',['eyebrow'=>'Transactions / Bookings','title'=>'New Booking','subtitle'=>'Create the central commercial transaction.']) ?>
<?php if(session()->has('errors')): ?><div class="alert alert-danger">Please check the form.</div><?php endif; ?>
<form method="post" action="<?=site_url('bookings/store')?>"><?=csrf_field()?><?=view('bookings/_form',['customers'=>$customers,'products'=>$products,'sources'=>$sources,'statuses'=>$statuses])?></form>
<?= $this->endSection() ?>
