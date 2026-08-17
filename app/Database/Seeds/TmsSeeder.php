<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TmsSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        /*
         * Idempotent seeder:
         * - Safe to run more than once in development.
         * - Existing records are skipped/updated only where needed.
         */

        // ---------------------------------------------------------
        // ROLES
        // ---------------------------------------------------------
        $roles = [
            ['name' => 'Administrator', 'description' => 'Full access'],
            ['name' => 'Sales',         'description' => 'Customer, quotation and booking'],
            ['name' => 'Finance',       'description' => 'Finance and accounting'],
        ];

        foreach ($roles as $row) {
            $exists = $db->table('roles')->where('name', $row['name'])->get()->getRow();

            if (!$exists) {
                $db->table('roles')->insert([
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // ---------------------------------------------------------
        // PERMISSIONS
        // ---------------------------------------------------------
        $permissions = [
            ['code' => 'booking.view',    'name' => 'View booking'],
            ['code' => 'booking.manage',  'name' => 'Manage booking'],
            ['code' => 'finance.view',    'name' => 'View finance'],
            ['code' => 'finance.post',    'name' => 'Post finance'],
            ['code' => 'accounting.view', 'name' => 'View accounting'],
            ['code' => 'accounting.post', 'name' => 'Post journal'],
            ['code' => 'whatsapp.send',   'name' => 'Send WhatsApp'],
        ];

        foreach ($permissions as $row) {
            $exists = $db->table('permissions')
                ->where('code', $row['code'])
                ->get()
                ->getRow();

            if (!$exists) {
                $db->table('permissions')->insert([
                    'code'        => $row['code'],
                    'name'        => $row['name'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // ---------------------------------------------------------
        // ACCOUNT GROUPS
        // ---------------------------------------------------------
        $groups = [
            [
                'code'        => 'BS-ASSET',
                'name'        => 'Assets',
                'report_type' => 'balance_sheet',
                'sort_order'  => 10,
            ],
            [
                'code'        => 'BS-LIAB',
                'name'        => 'Liabilities',
                'report_type' => 'balance_sheet',
                'sort_order'  => 20,
            ],
            [
                'code'        => 'BS-EQ',
                'name'        => 'Equity',
                'report_type' => 'balance_sheet',
                'sort_order'  => 30,
            ],
            [
                'code'        => 'PL-REV',
                'name'        => 'Revenue',
                'report_type' => 'profit_loss',
                'sort_order'  => 40,
            ],
            [
                'code'        => 'PL-COGS',
                'name'        => 'Cost of Sales',
                'report_type' => 'profit_loss',
                'sort_order'  => 50,
            ],
            [
                'code'        => 'PL-EXP',
                'name'        => 'Operating Expenses',
                'report_type' => 'profit_loss',
                'sort_order'  => 60,
            ],
        ];

        foreach ($groups as $row) {
            $exists = $db->table('account_groups')
                ->where('code', $row['code'])
                ->get()
                ->getRow();

            if (!$exists) {
                $db->table('account_groups')->insert([
                    'code'        => $row['code'],
                    'name'        => $row['name'],
                    'report_type' => $row['report_type'],
                    'parent_id'   => null,
                    'sort_order'  => $row['sort_order'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // Re-read group IDs because they may already exist.
        $g = [];

        foreach ($db->table('account_groups')->get()->getResult() as $row) {
            $g[$row->code] = $row->id;
        }

        // ---------------------------------------------------------
        // CHART OF ACCOUNTS
        // ---------------------------------------------------------
        $accounts = [
            [
                'code' => '1100',
                'name' => 'Cash',
                'account_type' => 'asset',
                'account_group' => 'BS-ASSET',
                'is_control_account' => 1,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '1200',
                'name' => 'Bank',
                'account_type' => 'asset',
                'account_group' => 'BS-ASSET',
                'is_control_account' => 1,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '1300',
                'name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'account_group' => 'BS-ASSET',
                'is_control_account' => 1,
                'allow_manual_posting' => 0,
            ],
            [
                'code' => '2100',
                'name' => 'Accounts Payable',
                'account_type' => 'liability',
                'account_group' => 'BS-LIAB',
                'is_control_account' => 1,
                'allow_manual_posting' => 0,
            ],
            [
                'code' => '3100',
                'name' => 'Owner Equity',
                'account_type' => 'equity',
                'account_group' => 'BS-EQ',
                'is_control_account' => 0,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '4100',
                'name' => 'Tour & Travel Revenue',
                'account_type' => 'revenue',
                'account_group' => 'PL-REV',
                'is_control_account' => 0,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '4200',
                'name' => 'Ticketing Revenue',
                'account_type' => 'revenue',
                'account_group' => 'PL-REV',
                'is_control_account' => 0,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '5100',
                'name' => 'Travel Cost of Sales',
                'account_type' => 'cogs',
                'account_group' => 'PL-COGS',
                'is_control_account' => 0,
                'allow_manual_posting' => 1,
            ],
            [
                'code' => '6100',
                'name' => 'Operating Expenses',
                'account_type' => 'expense',
                'account_group' => 'PL-EXP',
                'is_control_account' => 0,
                'allow_manual_posting' => 1,
            ],
        ];

        foreach ($accounts as $row) {
            $exists = $db->table('accounts')
                ->where('code', $row['code'])
                ->get()
                ->getRow();

            if (!$exists) {
                $db->table('accounts')->insert([
                    'code'                => $row['code'],
                    'name'                => $row['name'],
                    'account_type'        => $row['account_type'],
                    'parent_id'           => null,
                    'account_group_id'    => $g[$row['account_group']],
                    'is_control_account' => $row['is_control_account'],
                    'allow_manual_posting'=> $row['allow_manual_posting'],
                    'is_active'           => 1,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }
        }

        // ---------------------------------------------------------
        // FISCAL YEAR
        // ---------------------------------------------------------
        $year = (int) date('Y');

        $fiscalYear = $db->table('fiscal_years')
            ->where('year', $year)
            ->get()
            ->getRow();

        if (!$fiscalYear) {
            $db->table('fiscal_years')->insert([
                'year'       => $year,
                'start_date' => $year . '-01-01',
                'end_date'   => $year . '-12-31',
                'status'     => 'open',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ---------------------------------------------------------
        // FISCAL PERIODS
        // ---------------------------------------------------------
        $fy = $db->table('fiscal_years')->where('year', $year)->get()->getRow();
        if ($fy) {
            for ($month = 1; $month <= 12; $month++) {
                $start = sprintf('%04d-%02d-01', $year, $month);
                $end = date('Y-m-t', strtotime($start));
                $exists = $db->table('fiscal_periods')
                    ->where('fiscal_year_id', $fy->id)
                    ->where('period_no', $month)
                    ->get()->getRow();
                if (!$exists) {
                    $db->table('fiscal_periods')->insert([
                        'fiscal_year_id' => $fy->id,
                        'period_no' => $month,
                        'start_date' => $start,
                        'end_date' => $end,
                        'status' => 'open',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // ---------------------------------------------------------
        // JOURNALS
        // ---------------------------------------------------------
        $journals = [
            ['code' => 'SALES',    'name' => 'Sales Journal',    'journal_type' => 'sales'],
            ['code' => 'PURCHASE', 'name' => 'Purchase Journal', 'journal_type' => 'purchase'],
            ['code' => 'CASH',     'name' => 'Cash Journal',     'journal_type' => 'cash'],
            ['code' => 'BANK',     'name' => 'Bank Journal',     'journal_type' => 'bank'],
            ['code' => 'GENERAL',  'name' => 'General Journal',  'journal_type' => 'general'],
        ];

        foreach ($journals as $row) {
            $exists = $db->table('journals')
                ->where('code', $row['code'])
                ->get()
                ->getRow();

            if (!$exists) {
                $db->table('journals')->insert([
                    'code'        => $row['code'],
                    'name'        => $row['name'],
                    'journal_type' => $row['journal_type'],
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // ---------------------------------------------------------
        // PAYMENT METHODS
        // ---------------------------------------------------------
        $cash = $db->table('accounts')->where('code', '1100')->get()->getRow();
        $bank = $db->table('accounts')->where('code', '1200')->get()->getRow();

        $paymentMethods = [
            ['code' => 'CASH', 'name' => 'Cash', 'method_type' => 'cash', 'account' => $cash?->id],
            ['code' => 'BCA_TRANSFER', 'name' => 'BCA Transfer', 'method_type' => 'bank', 'account' => $bank?->id],
            ['code' => 'BANK_TRANSFER', 'name' => 'Bank Transfer', 'method_type' => 'bank', 'account' => $bank?->id],
            ['code' => 'QRIS', 'name' => 'QRIS', 'method_type' => 'bank', 'account' => $bank?->id],
        ];

        foreach ($paymentMethods as $row) {
            if (!$row['account']) {
                continue;
            }
            $exists = $db->table('payment_methods')->where('code', $row['code'])->get()->getRow();
            $data = [
                'code' => $row['code'],
                'name' => $row['name'],
                'method_type' => $row['method_type'],
                'clearing_account_id' => $row['account'],
                'is_active' => 1,
                'updated_at' => $now,
            ];
            if (!$exists) {
                $data['created_at'] = $now;
                $db->table('payment_methods')->insert($data);
            } else {
                $db->table('payment_methods')->where('id', $exists->id)->update($data);
            }
        }

        // ---------------------------------------------------------
        // DEFAULT TAX
        // ---------------------------------------------------------
        $taxAccount = $db->table('accounts')
            ->where('code', '2100')
            ->get()
            ->getRow();

        if ($taxAccount) {
            $exists = $db->table('taxes')
                ->where('code', 'NO-TAX')
                ->get()
                ->getRow();

            if (!$exists) {
                $db->table('taxes')->insert([
                    'code'          => 'NO-TAX',
                    'name'          => 'No Tax',
                    'rate'          => 0,
                    'tax_type'      => 'none',
                    'tax_account_id'=> $taxAccount->id,
                    'is_active'     => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        echo "TMS v1 seed completed successfully.\n";
    }
}