<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= view('components/page_header',['eyebrow'=>'Master Data / Customers','title'=>'Edit Customer','subtitle'=>'Update customer profile and contact information.']) ?>
<?php if(session()->has('errors')): ?><div class="alert alert-danger"><span class="alert-icon">!</span><div><?=implode('<br>',array_map('esc',session('errors')))?></div></div><?php endif; ?>
<form method="post" action="<?=site_url('customers/update/'.$customer['id'])?>"><?=csrf_field()?><?=view('customers/_form',['customer'=>$customer])?></form>
<?= $this->endSection() ?>
