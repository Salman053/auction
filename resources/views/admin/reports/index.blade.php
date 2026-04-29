<x-admin-layout :title="'Reports'">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Financial Reports</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Generate and export platform transactions, revenue, and user balances.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" class="inline-flex items-center gap-1.5 rounded-full bg-[#1877f2] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#166fe5]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Report
                </button>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Report Card 1 -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 flex flex-col justify-between dark:bg-zinc-900 dark:ring-white/10">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Monthly Revenue</h3>
                </div>
                <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">Comprehensive overview of platform fees, successful auction margins, and exchange rate gains for the current month.</p>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Last generated: 2 days ago</span>
                <button class="text-sm font-medium text-[#1877f2] hover:text-[#166fe5]">Generate</button>
            </div>
        </div>

        <!-- Report Card 2 -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 flex flex-col justify-between dark:bg-zinc-900 dark:ring-white/10">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Deposit Reconciliation</h3>
                </div>
                <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">Detailed breakdown of all user deposits, matching payment gateway records with internal ledger entries.</p>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Last generated: Today</span>
                <button class="text-sm font-medium text-[#1877f2] hover:text-[#166fe5]">Generate</button>
            </div>
        </div>

        <!-- Report Card 3 -->
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5 flex flex-col justify-between dark:bg-zinc-900 dark:ring-white/10">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">User Balances Liability</h3>
                </div>
                <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">Total snapshot of user wallet balances representing platform liability at a specific point in time.</p>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Last generated: 1 week ago</span>
                <button class="text-sm font-medium text-[#1877f2] hover:text-[#166fe5]">Generate</button>
            </div>
        </div>
    </div>
</x-admin-layout>
