<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $yahoo_auction_id
 * @property string|null $title
 * @property string|null $condition
 * @property int $starting_bid_yen
 * @property int $current_bid_yen
 * @property int $bid_count
 * @property string $status
 * @property int|null $winner_user_id
 * @property int|null $winning_bid_id
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property bool $auto_extension
 * @property string|null $seller_name
 * @property string|null $yahoo_seller_id
 * @property float|null $seller_rating
 * @property string|null $thumbnail_url
 * @property array<array-key, mixed>|null $image_urls
 * @property array<array-key, mixed>|null $raw
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property int $view_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $shipment_status
 * @property \Illuminate\Support\Carbon|null $bidder_confirmed_at
 * @property \Illuminate\Support\Carbon|null $admin_approved_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bid> $bids
 * @property-read int|null $bids_count
 * @property-read string $time_remaining
 * @property-read int $total_estimated_yen
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WatchlistItem> $watchlistItems
 * @property-read int|null $watchlist_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction endingSoon($hours = 24)
 * @method static \Database\Factories\AuctionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction shouldClose()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereAdminApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereAutoExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereBidCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereBidderConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereCurrentBidYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereImageUrls($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereSellerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereSellerRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereShipmentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereStartingBidYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereThumbnailUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereWinnerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereWinningBidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereYahooAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction whereYahooSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Auction withoutTrashed()
 */
	class Auction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $actor_user_id
 * @property string|null $guard
 * @property string $event
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $actor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereActorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereGuard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $auction_id
 * @property int $user_id
 * @property int $amount_yen
 * @property int|null $max_amount_yen
 * @property int $locked_amount_yen
 * @property string $status
 * @property string $placed_via
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property int|null $shipping_rate_id
 * @property-read \App\Models\Auction|null $auction
 * @property-read \App\Models\ShippingRate|null $shippingRate
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\BidFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereAmountYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereLockedAmountYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereMaxAmountYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid wherePlacedVia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereShippingRateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bid withoutTrashed()
 */
	class Bid extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $scheme
 * @property string $host
 * @property int $port
 * @property string|null $username
 * @property string|null $password
 * @property string|null $country
 * @property bool $is_active
 * @property int $success_count
 * @property int $failure_count
 * @property int|null $avg_response_ms
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $last_checked_at
 * @property \Illuminate\Support\Carbon|null $disabled_until
 * @property string|null $last_error
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScrapingLog> $scrapingLogs
 * @property-read int|null $scraping_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereAvgResponseMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereDisabledUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereFailureCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereLastCheckedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereLastError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereSuccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proxy withoutTrashed()
 */
	class Proxy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $run_uuid
 * @property int|null $proxy_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property int $auctions_created
 * @property int $auctions_updated
 * @property int $auctions_closed
 * @property int $auctions_failed
 * @property string|null $error_message
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Proxy|null $proxy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereAuctionsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereAuctionsCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereAuctionsFailed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereAuctionsUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereRunUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrapingLog whereUpdatedAt($value)
 */
	class ScrapingLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property array<array-key, mixed>|null $value
 * @property int|null $updated_by_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string|null $port
 * @property int $fee_yen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereFeeYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingRate whereUpdatedAt($value)
 */
	class ShippingRate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $requester_name
 * @property string|null $requester_email
 * @property string $subject
 * @property string $status
 * @property int|null $assigned_to_user_id
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicketMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereAssignedToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereRequesterEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereRequesterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket withoutTrashed()
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $support_ticket_id
 * @property int|null $author_user_id
 * @property string $body
 * @property bool $is_internal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\SupportTicket|null $ticket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereAuthorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereSupportTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketMessage withoutTrashed()
 */
	class SupportTicketMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \App\Enums\UserRole $role
 * @property \Illuminate\Support\Carbon|null $kyc_verified_at
 * @property int|null $bidding_multiplier_percent
 * @property \Illuminate\Support\Carbon|null $suspended_at
 * @property string $password
 * @property string|null $stripe_account_id
 * @property string|null $two_factor_secret
 * @property \Illuminate\Support\Carbon|null $two_factor_enabled_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $shipping_rate_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bid> $bids
 * @property-read int|null $bids_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\ShippingRate|null $shippingRate
 * @property-read \App\Models\Wallet|null $wallet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WatchlistItem> $watchlistItems
 * @property-read int|null $watchlist_items_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBiddingMultiplierPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKycVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereShippingRateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStripeAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $balance_yen
 * @property int $locked_balance_yen
 * @property int $withdrawal_locked_yen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletTransaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\WalletFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereBalanceYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereLockedBalanceYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereWithdrawalLockedYen($value)
 */
	class Wallet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $wallet_id
 * @property string $type
 * @property string $status
 * @property int $amount_yen
 * @property string|null $provider
 * @property string|null $provider_reference
 * @property int|null $requested_by_user_id
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $memo
 * @property string|null $receipt_path
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $requestedBy
 * @property-read \App\Models\Wallet $wallet
 * @method static \Database\Factories\WalletTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereAmountYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereApprovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereProviderReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereReceiptPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereRequestedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction withoutTrashed()
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $auction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Auction|null $auction
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem whereAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WatchlistItem whereUserId($value)
 */
	class WatchlistItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $amount_yen
 * @property string $status
 * @property string|null $destination_type
 * @property array<array-key, mixed>|null $destination_meta
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $memo
 * @property string|null $receipt_path
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereAmountYen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereApprovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereDestinationMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereDestinationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereReceiptPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WithdrawalRequest withoutTrashed()
 */
	class WithdrawalRequest extends \Eloquent {}
}

