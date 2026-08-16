<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= view('components/page_header',['eyebrow'=>'Master Data / Customers','title'=>'New Customer','subtitle'=>'Create a new customer profile.']) ?>
<?php if(session()->has('errors')): ?><div class="alert alert-danger"><span class="alert-icon">!</span><div><?=implode('<br>',array_map('esc',session('errors')))?></div></div><?php endif; ?>
<form method="post" action="<?=site_url('customers/store')?>"><?=csrf_field()?><?=view('customers/_form',['customer'=>[]])?></form>
<?= $this->endSection() ?>
