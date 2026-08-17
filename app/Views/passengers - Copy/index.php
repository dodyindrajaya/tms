<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= view('components/page_header', ['eyebrow' => 'Master Data', 'title' => 'Passengers', 'subtitle' => 'Manage passenger records']) ?>

<div class="card">
    <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <form method="get" style="margin:0"><input type="search" name="q" value="<?= esc($q) ?>" placeholder="Search passengers..."> <button class="btn">Search</button></form>
            <a class="btn btn-primary" href="<?= site_url('passengers/create') ?>">New Passenger</a>
        </div>

        <table class="table">
            <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Phone</th><th>Passport</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($passengers as $p): ?>
                    <tr>
                        <td><?= esc($p['id']) ?></td>
                        <td><?= esc($p['passenger_code']) ?></td>
                        <td><?= esc($p['full_name']) ?></td>
                        <td><?= esc($p['phone'] ?? '') ?></td>
                        <td><?= esc($p['passport_no'] ?? '') ?></td>
                        <td>
                            <a class="btn btn-sm" href="<?= site_url('passengers/edit/'.$p['id']) ?>">Edit</a>
                            <a class="btn btn-sm btn-danger" href="<?= site_url('passengers/delete/'.$p['id']) ?>" onclick="return confirm('Delete this passenger?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?= $pager->links() ?>
    </div>
 </div>

<?= $this->endSection() ?>
