<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\AccountGroupModel;

class ChartOfAccounts extends BaseController
{
    protected AccountModel $accountModel;
    protected AccountGroupModel $groupModel;

    private const ACCOUNT_TYPES = [
        'asset'     => 'Asset',
        'liability' => 'Liability',
        'equity'    => 'Equity',
        'revenue'   => 'Revenue',
        'cogs'      => 'Cost of Sales',
        'expense'   => 'Expense',
    ];

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->groupModel   = new AccountGroupModel();
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $type   = trim((string) $this->request->getGet('type'));
        $status = trim((string) $this->request->getGet('status'));
        $control = trim((string) $this->request->getGet('control'));

        $builder = $this->accountModel
            ->select('accounts.*, p.code AS parent_code, p.name AS parent_name, g.code AS group_code, g.name AS group_name')
            ->join('accounts p', 'p.id = accounts.parent_id', 'left')
            ->join('account_groups g', 'g.id = accounts.account_group_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('accounts.code', $q)
                ->orLike('accounts.name', $q)
                ->orLike('p.code', $q)
                ->orLike('p.name', $q)
                ->groupEnd();
        }

        if ($type !== '' && array_key_exists($type, self::ACCOUNT_TYPES)) {
            $builder->where('accounts.account_type', $type);
        }

        if ($status === 'active') {
            $builder->where('accounts.is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('accounts.is_active', 0);
        }

        if ($control === 'yes') {
            $builder->where('accounts.is_control_account', 1);
        } elseif ($control === 'no') {
            $builder->where('accounts.is_control_account', 0);
        }

        $accounts = $builder->orderBy('accounts.code', 'ASC')->paginate(20);

        $stats = [
            'total'   => $this->accountModel->countAllResults(),
            'active'  => $this->accountModel->where('is_active', 1)->countAllResults(),
            'control' => $this->accountModel->where('is_control_account', 1)->countAllResults(),
            'groups'  => $this->groupModel->countAllResults(),
        ];

        return view('accounting/accounts/index', [
            'title'        => 'Chart of Accounts',
            'accounts'     => $accounts,
            'pager'        => $this->accountModel->pager,
            'q'            => $q,
            'type'         => $type,
            'status'       => $status,
            'control'      => $control,
            'accountTypes' => self::ACCOUNT_TYPES,
            'stats'        => $stats,
        ]);
    }

    public function create()
    {
        return view('accounting/accounts/form', [
            'title'        => 'New Account',
            'account'      => [],
            'parents'      => $this->parentOptions(),
            'groups'       => $this->groupOptions(),
            'accountTypes' => self::ACCOUNT_TYPES,
            'mode'         => 'create',
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('error', 'Please check the account form.');
        }

        $data = $this->accountInput();

        $structureError = $this->structureError($data, null);
        if ($structureError !== null) {
            return redirect()->back()->withInput()->with('error', $structureError);
        }

        if (! $this->accountModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Account could not be saved.');
        }

        return redirect()->to(site_url('accounting/accounts'))
            ->with('success', 'Account created successfully.');
    }

    public function show(int $id)
    {
        $account = $this->accountWithRelations($id);

        if (! $account) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account not found.');
        }

        $db = db_connect();
        $usage = $db->table('journal_entry_lines')
            ->select('COUNT(*) AS line_count, COALESCE(SUM(debit),0) AS total_debit, COALESCE(SUM(credit),0) AS total_credit')
            ->where('account_id', $id)
            ->get()->getRowArray();

        $children = $db->table('accounts')
            ->where('parent_id', $id)
            ->orderBy('code', 'ASC')
            ->get()->getResultArray();

        return view('accounting/accounts/show', [
            'title'    => 'Account Detail',
            'account'  => $account,
            'usage'    => $usage ?: ['line_count' => 0, 'total_debit' => 0, 'total_credit' => 0],
            'children' => $children,
        ]);
    }

    public function edit(int $id)
    {
        $account = $this->accountModel->find($id);

        if (! $account) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account not found.');
        }

        return view('accounting/accounts/form', [
            'title'        => 'Edit Account',
            'account'      => $account,
            'parents'      => $this->parentOptions($id),
            'groups'       => $this->groupOptions(),
            'accountTypes' => self::ACCOUNT_TYPES,
            'mode'         => 'edit',
        ]);
    }

    public function update(int $id)
    {
        $account = $this->accountModel->find($id);

        if (! $account) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account not found.');
        }

        if (! $this->validate($this->validationRules($id))) {
            return redirect()->back()->withInput()->with('error', 'Please check the account form.');
        }

        $data = $this->accountInput();

        $structureError = $this->structureError($data, $id);
        if ($structureError !== null) {
            return redirect()->back()->withInput()->with('error', $structureError);
        }

        // A control account is normally system-managed. Keep it active/inactive,
        // but do not allow an inactive control account to be used as a parent.
        if (! $this->accountModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Account could not be updated.');
        }

        return redirect()->to(site_url('accounting/accounts'))
            ->with('success', 'Account updated successfully.');
    }

    public function toggle(int $id)
    {
        $account = $this->accountModel->find($id);

        if (! $account) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account not found.');
        }

        $newStatus = ! (bool) $account['is_active'];

        if ($newStatus && $account['parent_id'] !== null) {
            $parent = $this->accountModel->find((int) $account['parent_id']);
            if (! $parent || ! (bool) $parent['is_active']) {
                return redirect()->to(site_url('accounting/accounts'))
                    ->with('error', 'Activate the parent account first.');
            }
        }

        $this->accountModel->update($id, ['is_active' => $newStatus ? 1 : 0]);

        return redirect()->to(site_url('accounting/accounts'))
            ->with('success', $newStatus ? 'Account activated.' : 'Account deactivated.');
    }

    public function delete(int $id)
    {
        $account = $this->accountModel->find($id);

        if (! $account) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account not found.');
        }

        $db = db_connect();
        $hasLines = $db->table('journal_entry_lines')->where('account_id', $id)->countAllResults() > 0;
        $hasChildren = $db->table('accounts')->where('parent_id', $id)->countAllResults() > 0;

        if ($hasLines || $hasChildren) {
            return redirect()->to(site_url('accounting/accounts'))
                ->with('error', 'This account cannot be deleted because it is already used or has child accounts. Deactivate it instead.');
        }

        // Check restrictive accounting mappings before deleting an unused account.
        $mappingChecks = [
            ['table' => 'payment_methods', 'field' => 'clearing_account_id'],
            ['table' => 'taxes', 'field' => 'tax_account_id'],
            ['table' => 'invoice_items', 'field' => 'revenue_account_id'],
            ['table' => 'supplier_bill_items', 'field' => 'expense_account_id'],
            ['table' => 'bank_accounts', 'field' => 'account_id'],
        ];

        foreach ($mappingChecks as $check) {
            if ($this->tableHasColumn($check['table'], $check['field']) &&
                $db->table($check['table'])->where($check['field'], $id)->countAllResults() > 0) {
                return redirect()->to(site_url('accounting/accounts'))
                    ->with('error', 'This account is already mapped to another finance master. Deactivate it instead.');
            }
        }

        if (! $this->accountModel->delete($id)) {
            return redirect()->to(site_url('accounting/accounts'))->with('error', 'Account could not be deleted.');
        }

        return redirect()->to(site_url('accounting/accounts'))->with('success', 'Account deleted successfully.');
    }

    private function accountWithRelations(int $id): ?array
    {
        return $this->accountModel
            ->select('accounts.*, p.code AS parent_code, p.name AS parent_name, g.code AS group_code, g.name AS group_name')
            ->join('accounts p', 'p.id = accounts.parent_id', 'left')
            ->join('account_groups g', 'g.id = accounts.account_group_id', 'left')
            ->where('accounts.id', $id)
            ->first();
    }

    private function parentOptions(?int $excludeId = null): array
    {
        $builder = $this->accountModel->where('is_active', 1);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        $rows = $builder->orderBy('code', 'ASC')->findAll();
        $options = [];
        foreach ($rows as $row) {
            $options[$row['id']] = $row['code'] . ' — ' . $row['name'];
        }
        return $options;
    }

    private function groupOptions(): array
    {
        $rows = $this->groupModel->orderBy('sort_order', 'ASC')->orderBy('code', 'ASC')->findAll();
        $options = [];
        foreach ($rows as $row) {
            $options[$row['id']] = $row['code'] . ' — ' . $row['name'];
        }
        return $options;
    }

    private function validationRules(?int $id = null): array
    {
        $codeRule = 'required|max_length[20]';
        $codeRule .= $id === null
            ? '|is_unique[accounts.code]'
            : '|is_unique[accounts.code,id,' . $id . ']';

        return [
            'code'        => $codeRule,
            'name'        => 'required|min_length[2]|max_length[190]',
            'account_type'=> 'required|in_list[asset,liability,equity,revenue,cogs,expense]',
            'parent_id'   => 'permit_empty|integer',
            'account_group_id' => 'permit_empty|integer',
        ];
    }

    private function accountInput(): array
    {
        return [
            'code'                 => strtoupper(trim((string) $this->request->getPost('code'))),
            'name'                 => trim((string) $this->request->getPost('name')),
            'account_type'         => trim((string) $this->request->getPost('account_type')),
            'parent_id'            => $this->nullableInt($this->request->getPost('parent_id')),
            'account_group_id'     => $this->nullableInt($this->request->getPost('account_group_id')),
            'is_control_account'   => $this->request->getPost('is_control_account') ? 1 : 0,
            'allow_manual_posting' => $this->request->getPost('allow_manual_posting') ? 1 : 0,
            'is_active'            => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function nullableInt($value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }

    private function structureError(array $data, ?int $currentId): ?string
    {
        if ($data['parent_id'] !== null) {
            $parent = $this->accountModel->find((int) $data['parent_id']);
            if (! $this->validParent((int) $data['parent_id'], $currentId)) {
                return 'The selected parent account is invalid, inactive, or would create a circular hierarchy.';
            }

            if ($parent && $parent['account_type'] !== $data['account_type']) {
                return 'Parent and child accounts must use the same account type.';
            }
        }

        if ($data['account_group_id'] !== null) {
            $group = $this->groupModel->find((int) $data['account_group_id']);
            if (! $group) {
                return 'The selected reporting group does not exist.';
            }

            $balanceSheetTypes = ['asset', 'liability', 'equity'];
            $expectedReport = in_array($data['account_type'], $balanceSheetTypes, true)
                ? 'balance_sheet'
                : 'profit_loss';

            if ($group['report_type'] !== $expectedReport) {
                return 'The reporting group does not match the selected account type.';
            }
        }

        return null;
    }

    private function validParent(int $parentId, ?int $currentId): bool
    {
        if ($currentId !== null && $parentId === $currentId) {
            return false;
        }

        $parent = $this->accountModel->find($parentId);
        if (! $parent || ! (bool) $parent['is_active']) {
            return false;
        }

        if ($currentId === null) {
            return true;
        }

        $seen = [];
        $cursor = $parentId;
        while ($cursor !== null) {
            if (isset($seen[$cursor])) {
                return false;
            }
            $seen[$cursor] = true;

            if ($cursor === $currentId) {
                return false;
            }

            $row = $this->accountModel->select('id,parent_id')->find($cursor);
            $cursor = $row ? ($row['parent_id'] !== null ? (int) $row['parent_id'] : null) : null;
        }

        return true;
    }

    private function tableHasColumn(string $table, string $column): bool
    {
        return db_connect()->getFieldNames($table) && in_array($column, db_connect()->getFieldNames($table), true);
    }
}
