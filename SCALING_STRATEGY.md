# Yahoo Auction Scaling Strategy & Commitment

## Objective

To scale the WatchHub scraping infrastructure to support 2,000,000+ active auction data (of the types (mobile,accessaires,jewelwery, computer, vehicle etc like all things that are listed and listing on the platform)) points with comprehensive coverage across all categories, including specialized monitoring for low-price items (0 Yen, 1 Yen, <100 Yen).

## 1. Research Findings & Current Limitations

- **Sequential Bottleneck:** The current `ScrapeYahoo` command processes items one-by-one with a fixed delay. At 2 seconds per item, 2 million items would take ~46 days to sync once. (dont jump direct to 2 milloion etc its a example ok )
- **Discovery Gap:** The system is currently keyword-based. Scaling requires a systematic category-traversal engine to ensure "each and every thing" is captured.
- **Proxy Capacity:** Currently, there are 0 active proxies. Large-scale scraping requires a pool of thousands of rotating residential IPs to avoid blocks.
- **Database Throughput:** Standard Eloquent `create`/`update` calls for 2 million rows will cause significant overhead.

## 2. Proposed Architecture (The "Commitment")

### A. Distributed Scraping Engine (Parallelization)

- **Laravel Horizon/Redis:** Move from synchronous commands to a job-based architecture.
- **Worker Scaling:** Deploy 50-100 concurrent workers.
- **Concurrency Control:** Implement per-proxy and per-category rate limiting to stay under Yahoo's detection thresholds.

### B. High-Volume Proxy Infrastructure

- **Residential Proxy Integration:** Integrate with free providers or if there is no free so than leave for now || use proxy-free scraping methods using SOCKS5/HTTP protocols.
- **Automatic Health Checks:** Implement a circuit breaker pattern to disable failing/blocked proxies automatically.

### C. Discovery & Crawling Strategy

- **Category Tree Crawler:**
    1.  Crawl the complete Yahoo Auction category hierarchy (~20,000 leaf categories).
    2.  Store and cache this map to drive systematic scraping.
- **Breadth-First Iteration:**
    - Iterate through all leaf categories instead of keywords.
    - Use "Recently Listed" sorting to find new items efficiently.
- **Targeted Price Monitoring:**
    - Specialized workers to scrape search filters for `max_price=1` and `max_price=100` across top-level categories.

### D. Data Synchronization Tiers

To keep 2 million items "fresh," we will use a tiered approach:

1.  **Tier 1 (Ending Soon):** Sync every 1-2 minutes for auctions ending in the next 15 minutes.
2.  **Tier 2 (New Items):** Sync every 10-15 minutes to discover newly listed items.
3.  **Tier 3 (Active/Stable):** Full sync once every 6-12 hours for all other active items.

### E. Database Optimization

- **Batch Upserts:** Use MySQL `INSERT ... ON DUPLICATE KEY UPDATE` to process items in batches of 500-1000.
- **Partitioning/Indexing:** Ensure `ends_at`, `current_bid_yen`, and `yahoo_auction_id` are heavily indexed for fast retrieval and updates.

## 3. Implementation Roadmap

### Phase 1: Foundation (Current Status -> 10k Items)

- Set up Laravel Horizon and basic Redis queues.
- Add the first batch of 100+ proxies.

### Phase 2: Systematic Discovery (10k -> 500k Items)

- Implement Category Tree Crawler.
- Transition from keyword search to Category-based discovery.
- Implement specialized "1 Yen" filter scrapers.

### Phase 3: Full Scale (500k -> 2M+ Items)

- Horizontal scaling of workers (multiple server nodes if necessary).
- Advanced monitoring dashboard for scraper health.
- Delta-syncing logic to minimize redundant requests.

## 4. Progress Update (May 12, 2026)

- **Research Complete:** Identified core bottlenecks in synchronous processing and keyword discovery.
- **Documentation Created:** This strategy document serves as the roadmap for development.
- **Next Steps:** Begin implementation of Category Tree Crawler and Worker-based parallelization.

Important note proceed step by step ok like first do the phase one and than write some test if the tests are passed than stop and give sugeestion ok so i will give you sugesstion and further instruction so than we jump to next phase ok
