    public function journal()
    {
        $db = db_connect();

        $entries = $db->table('journal_entries je')
            ->select(
                "je.id,
                 je.entry_no,
                 je.entry_date,
                 je.reference_type,
                 je.reference_id,
                 je.description,
                 je.status,
                 je.posted_at,
                 j.code AS journal_code,
                 j.name AS journal_name,
                 COALESCE(SUM(l.debit), 0) AS total_debit,
                 COALESCE(SUM(l.credit), 0) AS total_credit"
            )
            ->join('journals j', 'j.id = je.journal_id', 'left')
            ->join('journal_entry_lines l', 'l.journal_entry_id = je.id', 'left')
            ->groupBy(
                'je.id, je.entry_no, je.entry_date, je.reference_type,
                 je.reference_id, je.description, je.status, je.posted_at,
                 j.code, j.name'
            )
            ->orderBy('je.entry_date', 'DESC')
            ->orderBy('je.id', 'DESC')
            ->get()
            ->getResultArray();

        $totalDebit  = 0.0;
        $totalCredit = 0.0;

        foreach ($entries as $entry) {
            $totalDebit  += (float) ($entry['total_debit'] ?? 0);
            $totalCredit += (float) ($entry['total_credit'] ?? 0);
        }

        return view('accounting/journal', [
            'title'       => 'Journal Entries',
            'entries'     => $entries,
            'totalDebit'  => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }