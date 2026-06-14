<?php

namespace App\Http\Controllers\PayLatter;

use App\DataTables\PayLatterAccountDataTable;
use App\DataTables\PayLatterTransactionDataTable;
use App\Http\Controllers\Controller;
use App\Models\PakDul\PayLatterAccount;
use App\Models\PakDul\PayLatterTransaction;
use Illuminate\Http\Request;

class PayLatterDataTableController extends Controller
{
    /**
     * Display paylatter account datatable
     */
    public function accountIndex(PayLatterAccountDataTable $dataTable)
    {
        return $dataTable->render('paylatter.account.index');
    }

    /**
     * Display paylatter transaction datatable
     */
    public function transactionIndex(PayLatterTransactionDataTable $dataTable)
    {
        return $dataTable->render('paylatter.transaction.index');
    }

    /**
     * Display the specified paylatter account
     */
    public function showAccount(PayLatterAccount $payLatterAccount)
    {
        $payLatterAccount->load(['user', 'toko', 'transactions']);
        return view('paylatter.account.show', compact('payLatterAccount'));
    }

    /**
     * Show the form for editing the specified paylatter account
     */
    public function editAccount(PayLatterAccount $payLatterAccount)
    {
        return view('paylatter.account.edit', compact('payLatterAccount'));
    }

    /**
     * Update the specified paylatter account
     */
    public function updateAccount(Request $request, PayLatterAccount $payLatterAccount)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'status' => 'required|in:active,suspended,pending,closed',
        ]);

        $payLatterAccount->update($validated);

        return redirect()
            ->route('paylatter.account.index')
            ->with('success', 'Akun Paylatter berhasil diperbarui');
    }

    /**
     * Display the specified paylatter transaction
     */
    public function showTransaction(PayLatterTransaction $payLatterTransaction)
    {
        $payLatterTransaction->load(['account.user', 'account.toko']);
        return view('paylatter.transaction.show', compact('payLatterTransaction'));
    }

    /**
     * Show the form for editing paylatter transaction status
     */
    public function editTransaction(PayLatterTransaction $payLatterTransaction)
    {
        if ($payLatterTransaction->status === 'paid') {
            return redirect()
                ->back()
                ->with('error', 'Transaksi yang sudah lunas tidak dapat diubah');
        }

        return view('paylatter.transaction.edit', compact('payLatterTransaction'));
    }

    /**
     * Update paylatter transaction status
     */
    public function updateTransaction(Request $request, PayLatterTransaction $payLatterTransaction)
    {
        if ($payLatterTransaction->status === 'paid') {
            return redirect()
                ->back()
                ->with('error', 'Transaksi yang sudah lunas tidak dapat diubah');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue,cancelled',
        ]);

        $payLatterTransaction->update($validated);

        // If status changed to paid, we might need to update the account's used_credit
        if ($validated['status'] === 'paid' && $payLatterTransaction->getOriginal('status') !== 'paid') {
            $account = $payLatterTransaction->account;
            $account->used_credit -= $payLatterTransaction->principal_amount;
            $account->available_credit += $payLatterTransaction->principal_amount;
            $account->successful_payments += 1;
            $account->save();
        }

        return redirect()
            ->route('paylatter.transaction.index')
            ->with('success', 'Status transaksi berhasil diperbarui');
    }
}
