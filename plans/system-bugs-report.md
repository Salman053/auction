# System Bug Report & Resolution Strategy: WatchHub

**Date:** May 11, 2026
**Status:** Research Completed / Planning Phase
**Scope:** Core Bidding, Wallet Management, and Auction Settlement Logic

---

## 1. Executive Summary
An investigation into the WatchHub application has revealed several critical logic errors in the bidding and settlement systems. These bugs primarily stem from a lack of synchronization between internal system states and real-time Yahoo Japan Auction data. If left unaddressed, these issues will lead to financial discrepancies, incorrect auction wins, and potential loss of user trust.

---

## 2. Identified Bugs & Technical Breakdown

### BUG-001: Auction Settlement Inconsistency (Internal vs. External)
*   **Description:** `AuctionSettlementService` awards a "Won" status to the highest internal bidder without verifying if they were outbid by a Yahoo user on the actual platform.
*   **Impact:** **CRITICAL**. Users will be charged for auctions they did not actually win on Yahoo.
*   **Root Cause:** `settleAuction` only looks at internal `bids` table statuses and does not check the `current_bid_yen` synced from Yahoo.

### BUG-002: Missing Scraper Reconciliation
*   **Description:** The `ScrapeYahoo` command updates the `current_bid_yen` from Yahoo but fails to trigger a reconciliation with internal bids.
*   **Impact:** **CRITICAL**. Internal high bidders remain marked as "active" (winning) even when the Yahoo price has surpassed their `max_amount_yen`. Funds remain locked for auctions that are already lost.
*   **Root Cause:** No post-scrape hook exists to compare the new Yahoo price against the `max_amount_yen` of internal bidders.

### BUG-003: Bidding Capacity Inflation (Pending Withdrawals)
*   **Description:** The bidding capacity (500% multiplier) is calculated incorrectly when a user has a pending withdrawal.
*   **Impact:** **STANDARD**. Users can place bids using funds that are already designated for withdrawal.
*   **Current Logic:** `(balance * multiplier) - withdrawal_locked`
*   **Correct Logic:** `(balance - withdrawal_locked) * multiplier`

### BUG-004: Incomplete Increment Table
*   **Description:** `ProxyBiddingService::getIncrement` stops at the ¥50,000 tier.
*   **Impact:** **STANDARD**. Internal proxy bidding will use incorrect increments for high-value items.
*   **Root Cause:** The method only implements the first four tiers of the Yahoo Japan Auction increment table.

---

## 3. Proposed Resolution Strategy

### Phase 1: Core Logic Hardening
1.  **Correct Bidding Capacity:** Update `BiddingService::placeBid` to use "Effective Balance" (Balance - Pending Withdrawals).
2.  **Complete Increment Table:** Update `ProxyBiddingService::getIncrement` to include all Yahoo tiers up to ¥1M+.

### Phase 2: Real-time Reconciliation Engine
1.  **Implement AuctionReconciliationService:** Create a service that compares the latest Yahoo `current_bid_yen` with internal `max_amount_yen`.
2.  **Triggering:** Call this service from `ScrapeYahoo` command after each auction update.

### Phase 3: Secure Settlement
1.  **Enhance AuctionSettlementService:** Add a mandatory verification step in `settleAuction` to check if `internal_max_bid >= final_yahoo_price`.

---

## 4. Verification Plan
*   **Automated Testing:** Regression tests for capacity calculation and reconciliation logic.
*   **Manual Verification:** End-to-end test of a scraping run on a known outbid item.
