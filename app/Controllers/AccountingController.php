<?php

namespace App\Controllers;

class AccountingController extends BaseController
{
    public function journal()
    {
        $db = db_connect();
        $entries = $db->table('journal_entries je')
            ->select('je.*, j.code AS journal_code, j.name AS journal_name')
            ->join('journals j','j.id=je.journal_id','left')
            ->orderBy('je.entry_date','DESC')->orderBy('je.id','DESC')
            ->get()->getResultArray();

        return view('accounting/journal', ['title'=>'Journal Entries','entries'=>$entries]);
    }

    public function gl()
    {
        $db = db_connect();
        $lines = $db->table('journal_entry_lines l')
            ->select('l.*, je.entry_no, je.entry_date, a.code AS account_code, a.name AS account_name')
            ->join('journal_entries je','je.id=l.journal_entry_id','left')
            ->join('accounts a','a.id=l.account_id','left')
            ->where('je.status','posted')
            ->orderBy('je.entry_date','DESC')->orderBy('l.id','DESC')
            ->get()->getResultArray();

        return view('accounting/gl', ['title'=>'General Ledger','lines'=>$lines]);
    }
}
