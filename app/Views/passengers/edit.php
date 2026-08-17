<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= view('components/page_header', ['eyebrow' => 'Master Data', 'title' => 'Edit Passenger', 'subtitle' => 'Update passenger details']) ?>

<form method="post" action="<?= site_url('passengers/update/'.$passenger['id']) ?>">
    <?= csrf_field() ?>
    <?= view('passengers/_form', ['passenger' => $passenger]) ?>
    <div style="margin-top:12px"><a class="btn btn-secondary" href="<?= site_url('passengers') ?>">Cancel</a> <button class="btn btn-primary">Save Changes</button></div>
</form>

<?= $this->endSection() ?>
