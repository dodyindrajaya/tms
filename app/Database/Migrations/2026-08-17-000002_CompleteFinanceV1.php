<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompleteFinanceV1 extends Migration
{
    public function up()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        // Ensure the standard cash/bank journals exist.
        $journals = [
            ['code'=>'CASH','name'=>'Cash Journal','journal_type'=>'cash'],
            ['code'=>'BANK','name'=>'Bank Journal','journal_type'=>'bank'],
            ['code'=>'SALES','name'=>'Sales Journal','journal_type'=>'sales'],
            ['code'=>'PURCHASE','name'=>'Purchase Journal','journal_type'=>'purchase'],
            ['code'=>'GENERAL','name'=>'General Journal','journal_type'=>'general'],
        ];
        foreach ($journals as $j) {
            $exists = $db->table('journals')->where('code',$j['code'])->get()->getRow();
            if (!$exists) {
                $db->table('journals')->insert($j + ['is_active'=>1,'created_at'=>$now,'updated_at'=>$now]);
            }
        }

        $accounts = [];
        foreach (['1100','1200','1300'] as $code) {
            $row = $db->table('accounts')->where('code',$code)->get()->getRow();
            if ($row) $accounts[$code] = (int)$row->id;
        }

        if (!empty($accounts['1100']) || !empty($accounts['1200'])) {
            $methods = [
                ['code'=>'CASH','name'=>'Cash','method_type'=>'cash','account'=>$accounts['1100'] ?? null],
                ['code'=>'BCA_TRANSFER','name'=>'BCA Transfer','method_type'=>'bank','account'=>$accounts['1200'] ?? null],
                ['code'=>'BANK_TRANSFER','name'=>'Bank Transfer','method_type'=>'bank','account'=>$accounts['1200'] ?? null],
                ['code'=>'QRIS','name'=>'QRIS','method_type'=>'bank','account'=>$accounts['1200'] ?? null],
            ];
            foreach ($methods as $m) {
                if (!$m['account']) continue;
                $exists = $db->table('payment_methods')->where('code',$m['code'])->get()->getRow();
                $data = [
                    'code'=>$m['code'],'name'=>$m['name'],'method_type'=>$m['method_type'],
                    'clearing_account_id'=>$m['account'],'is_active'=>1,'updated_at'=>$now,
                ];
                if (!$exists) { $data['created_at']=$now; $db->table('payment_methods')->insert($data); }
                else { $db->table('payment_methods')->where('id',$exists->id)->update($data); }
            }
        }

        // Create monthly periods for the current fiscal year if missing.
        $year = (int)date('Y');
        $fy = $db->table('fiscal_years')->where('year',$year)->get()->getRow();
        if (!$fy) {
            $db->table('fiscal_years')->insert([
                'year'=>$year,'start_date'=>$year.'-01-01','end_date'=>$year.'-12-31',
                'status'=>'open','created_at'=>$now,'updated_at'=>$now,
            ]);
            $fy = $db->table('fiscal_years')->where('year',$year)->get()->getRow();
        }
        if ($fy) {
            for ($month=1;$month<=12;$month++) {
                $exists = $db->table('fiscal_periods')
                    ->where('fiscal_year_id',$fy->id)->where('period_no',$month)->get()->getRow();
                if (!$exists) {
                    $start = sprintf('%04d-%02d-01',$year,$month);
                    $db->table('fiscal_periods')->insert([
                        'fiscal_year_id'=>$fy->id,'period_no'=>$month,
                        'start_date'=>$start,'end_date'=>date('Y-m-t',strtotime($start)),
                        'status'=>'open','created_at'=>$now,'updated_at'=>$now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        // Keep reference/master data intact on rollback.
    }
}
