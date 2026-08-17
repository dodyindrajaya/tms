<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= view('components/page_header', ['eyebrow' => 'Master Data', 'title' => 'New Passenger', 'subtitle' => 'Create a passenger record']) ?>

<form method="post" action="<?= site_url('passengers/store') ?>">
    <?= csrf_field() ?>
    <?= view('passengers/_form') ?>
    <div style="margin-top:12px"><a class="btn btn-secondary" href="<?= site_url('passengers') ?>">Cancel</a> <button class="btn btn-primary">Create Passenger</button></div>
</form>

<?= $this->endSection() ?>
