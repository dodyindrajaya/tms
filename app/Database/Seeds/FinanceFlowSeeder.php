<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinanceFlowSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        // Tax payable account. Keep 2100 as Accounts Payable.
        $group = $db->table('account_groups')->where('code','BS-LIAB')->get()->getRow();
        if ($group) {
            $exists = $db->table('accounts')->where('code','2200')->get()->getRow();
            if (!$exists) {
                $db->table('accounts')->insert([
                    'code'=>'2200','name'=>'Tax Payable','account_type'=>'liability','parent_id'=>null,
                    'account_group_id'=>$group->id,'is_control_account'=>0,'allow_manual_posting'=>1,
                    'is_active'=>1,'created_at'=>$now,'updated_at'=>$now,
                ]);
            }
        }

        $cash = $db->table('accounts')->where('code','1100')->get()->getRow();
        $bank = $db->table('accounts')->where('code','1200')->get()->getRow();
        if ($cash) {
            $this->ensurePaymentMethod('CASH','Cash','cash',(int)$cash->id,$now);
        }
        if ($bank) {
            $this->ensurePaymentMethod('BANK','Bank Transfer','bank',(int)$bank->id,$now);
        }

        $this->ensureJournal('SALES','Sales Journal','sales',$now);
        $this->ensureJournal('PURCHASE','Purchase Journal','purchase',$now);
        $this->ensureJournal('CASH','Cash Journal','cash',$now);
        $this->ensureJournal('BANK','Bank Journal','bank',$now);
    }

    private function ensurePaymentMethod(string $code,string $name,string $type,int $accountId,string $now): void
    {
        $db=$this->db;
        $row=$db->table('payment_methods')->where('code',$code)->get()->getRow();
        if(!$row){
            $db->table('payment_methods')->insert([
                'code'=>$code,'name'=>$name,'method_type'=>$type,'clearing_account_id'=>$accountId,
                'is_active'=>1,'created_at'=>$now,'updated_at'=>$now,
            ]);
        } elseif ((int)$row->clearing_account_id !== $accountId) {
            $db->table('payment_methods')->where('id',$row->id)->update(['clearing_account_id'=>$accountId,'updated_at'=>$now]);
        }
    }

    private function ensureJournal(string $code,string $name,string $type,string $now): void
    {
        $db=$this->db;
        if(!$db->table('journals')->where('code',$code)->get()->getRow()){
            $db->table('journals')->insert(['code'=>$code,'name'=>$name,'journal_type'=>$type,'is_active'=>1,'created_at'=>$now,'updated_at'=>$now]);
        }
    }
}
