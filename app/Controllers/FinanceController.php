<?php

namespace App\Controllers;

class FinanceController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $monthStart = date('Y-m-01');
        $nextMonth = date('Y-m-01', strtotime('+1 month'));

        $ar = (float) ($db->table('invoices')->selectSum('outstanding_amount', 'total')->whereIn('status', ['posted','partial','overdue'])->get()->getRow()->total ?? 0);
        $ap = (float) ($db->table('supplier_bills')->selectSum('outstanding_amount', 'total')->whereIn('status', ['posted','partial','overdue'])->get()->getRow()->total ?? 0);
        $monthDebit = (float) ($db->table('journal_entry_lines l')->selectSum('l.debit', 'total')->join('journal_entries je','je.id=l.journal_entry_id')->where('je.status','posted')->where('je.entry_date >=',$monthStart)->where('je.entry_date <',$nextMonth)->get()->getRow()->total ?? 0);
        $monthCredit = (float) ($db->table('journal_entry_lines l')->selectSum('l.credit', 'total')->join('journal_entries je','je.id=l.journal_entry_id')->where('je.status','posted')->where('je.entry_date >=',$monthStart)->where('je.entry_date <',$nextMonth)->get()->getRow()->total ?? 0);

        $cashBank = (float) ($db->table('journal_entry_lines l')->selectSum('l.debit','debit')->selectSum('l.credit','credit')->join('journal_entries je','je.id=l.journal_entry_id')->join('accounts a','a.id=l.account_id')->where('je.status','posted')->whereIn('a.code',['1100','1200'])->get()->getRow()->debit ?? 0);
        $cashBankRow = $db->table('journal_entry_lines l')->selectSum('l.debit','debit')->selectSum('l.credit','credit')->join('journal_entries je','je.id=l.journal_entry_id')->join('accounts a','a.id=l.account_id')->where('je.status','posted')->whereIn('a.code',['1100','1200'])->get()->getRow();
        $cashBank = (float)($cashBankRow->debit ?? 0) - (float)($cashBankRow->credit ?? 0);

        $recent = $db->table('journal_entries je')->select('je.*, j.code AS journal_code, j.name AS journal_name')->join('journals j','j.id=je.journal_id','left')->where('je.status','posted')->orderBy('je.entry_date','DESC')->orderBy('je.id','DESC')->limit(8)->get()->getResultArray();

        return view('finance/dashboard', [
            'title'=>'Finance Dashboard',
            'stats'=>['ar'=>$ar,'ap'=>$ap,'monthDebit'=>$monthDebit,'monthCredit'=>$monthCredit,'cashBank'=>$cashBank],
            'recent'=>$recent,
        ]);
    }
}
