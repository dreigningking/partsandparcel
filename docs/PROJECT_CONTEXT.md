# Parts & Parcel — Project Context

## Purpose

This is the working architectural context for AI-assisted development of Parts & Parcel.

The actual migrations and application code remain authoritative for implementation. If this document and the code disagree, flag the discrepancy rather than silently redesigning the system.

## Product

Parts & Parcel is a marketplace and community platform for buying and selling devices, parts, vehicles, equipment and other physical goods, while also supporting negotiated offers, invoices, payments, delivery and services.

Core domains:

- Identity/account
- Catalog/marketplace
- Community
- Offers/negotiation
- Cart
- Billing
- Logistics
- Services
- Trust/resolution
- Subscriptions
- Platform finance

## Catalog model

The core hierarchy is:

Category → Brand → Model → Item → Component

An `item` is a specific physical thing held by a user, such as a particular laptop, vehicle or damaged device.

A `component` belongs to an item and represents a physical component such as a screen, motherboard or battery.

A `listing` is what the buyer actually browses and interacts with.

A listing uses a polymorphic relationship to an item or component:

- `assetable_id`
- `assetable_type`

Do not introduce redundant `asset`, `component_type`, `model_component` or `inventory_item` abstractions merely to repeat this model.

Quantity belongs on the listing.

A seller can list:

1. A complete item.
2. An individual component.
3. A damaged/scrap item.
4. Components separately and later list the whole item again.

## Marketplace navigation

A category/model/search result has four contextual tabs:

- Complete
- Parts
- Scrap
- Community

These are four different buying intents around the same category/model, not four different inventory systems.

### Complete

Working/complete devices, vehicles, equipment and similar goods.

### Parts

Individual parts/components related to the current category/model.

Customer-facing navigation should use “Parts”, not “Components”.

### Scrap

Damaged, broken, incomplete or salvageable physical items whose components may have value.

Use “Scrap” as the main customer-facing label. Listing labels may include SCRAP, SALVAGE, DAMAGED and FOR PARTS.

### Community

Contextual discussions around the current category/model, including requests for parts, sellers, advice, repairs and delivery.

Each tab has its own relevant filters, lists/grids and pagination. Do not show irrelevant filters.

## Categories

Top-level categories include:

- Electronics
- Appliances
- Vehicles
- Equipment
- Construction
- Industrial
- Agricultural

Desktop uses a sticky first header row and a second category mega-menu row.

Vehicles and machinery are first-class marketplace categories.

## Users

There is one user identity. A user can act as:

- buyer
- seller
- requester
- responder
- repairer/service provider
- delivery provider/helper

Do not create separate buyer and seller accounts.

## Cart

The platform uses:

- `carts`
- `cart_items`

A cart has `buyer_id` and `seller_id`, allowing cart items to be grouped by seller.

There is no `order_groups` table.

A cart can have many cart items, offers and invoices, with shipments connected through the resulting transaction/invoices.

A buyer can purchase normally or make an offer from the cart.

## Favorites

There is a `wishlists` table.

There is intentionally no `wishlist_items` table.

The customer-facing name is “Favorites”.

## Community

A discussion/request is public.

A discussion can have many responses.

A response is public and can have many offers.

The core structure is:

Discussion → Responses → Offers

Discussion types include:

- item
- service
- delivery
- advice

Community is closely connected to the marketplace and is not merely a generic forum.

## Offers

An offer identifies:

- `sender_id`
- `recipient_id`

Do not base the fundamental relationship on buyer/seller because offers can involve:

- buyer and seller
- customer and repairer
- customer and delivery provider
- helper and requester

An offer can originate from:

- a cart
- a community response

Therefore `cart_id` and `response_id` may be nullable.

An offer has many `offer_items`.

An offer item may contain:

- `listing_id` nullable
- `description`
- `quantity`
- `unit_price`
- warranty information

`listing_id` is nullable because an offer item may represent a service or delivery arrangement that is not itself a listing.

Offer discount is at the offer level.

Offer expiry may be seller-defined or use an application/profile default.

Offers retain `delivery_method`:

- `buyer_responsible`
- `seller_responsible`
- `platform_responsible`

A negotiation is a sequence of complete offers:

Offer → Counter Offer → Counter Offer → Accepted Offer

Do not automatically introduce a parent-offer architecture. The approved direction is sender/recipient plus chronological offers unless implementation later proves a parent relationship necessary.

## Invoices

Invoices are the final commercial records for ordinary purchases and negotiated transactions.

An invoice can relate to:

- buyer
- seller
- cart
- accepted offer

An invoice has many invoice items.

Invoice items use a polymorphic `itemable` relationship and may represent:

- listing
- shipment
- service job
- other explicitly supported transaction components

Invoice items contain description, quantity, unit price, amount and applicable warranty data.

Invoice discount is at the invoice level.

Payments belong in the `payments` table.

Whatever is agreed in an accepted offer is copied into the final invoice/invoice items.

## Delivery and shipments

There are three delivery responsibility modes:

1. Buyer responsible
2. Seller responsible
3. Platform responsible

The method remains relevant even though a shipment has sender/receiver data because it describes who is responsible for arranging delivery.

### Buyer responsible

The buyer can find a delivery provider through Community after the original transaction.

Flow:

Original transaction → delivery request → responses → offer → shipment → invoice → payment

### Seller responsible

No community request/response is required merely because the seller arranges delivery.

### Platform responsible

The platform can use integrated logistics.

For the MVP, provider integration can remain minimal.

### Shipment

Use:

- `sender_id`
- `receiver_id`

rather than interpreting consignor/consignee as responsibility.

The shipment records actual movement of goods, including provider name, tracking, status, locations/address snapshots, fee and timestamps.

`provider_id` is not required for the MVP.

A shipment has many shipment items.

Shipment items use a nullable polymorphic `itemable` and record what is actually moved.

## Locations

Use one `locations` concept for:

- buyer saved addresses
- seller stock locations
- shipment-related locations

Do not create a separate `user_addresses` system.

Seller listings can come from different stock locations. The correct origin location must be selected when creating a shipment.

Shipment records should retain address snapshots so later changes to saved locations do not rewrite historical shipment information.

## Services

Services are a real part of the platform.

Examples:

- repair
- installation
- maintenance
- diagnostics

A service can be negotiated through Community and included in an offer and invoice.

### Service jobs

`service_jobs` represent an actual service transaction/performed service.

A service job supports:

- customer
- provider
- optional `item_id`
- external item description when the item is not registered on the platform
- offer
- invoice
- title
- description
- status
- location
- scheduled/started/completed timestamps
- warranty
- notes

The serviced item can be:

1. An item purchased through Parts & Parcel.
2. An item registered on Parts & Parcel.
3. An item that does not exist in the platform database.

Therefore `item_id` is nullable.

### Service reviews

Completed service jobs can receive service reviews. These are useful for showing verified service history/reputation on a provider's public profile.

Do not build a full service marketplace/catalog system for the MVP.

## Warranty

Warranty information may be negotiated in offers.

When an offer is accepted, agreed warranty information is copied into final transaction/service records.

Warranty can apply to:

- product purchases
- repairs
- installations
- other applicable services

Do not assume a damaged/scrap item automatically has a normal warranty.

## Trust and resolution

The platform includes concepts for:

- issues
- issue items
- returns
- return items
- replacements
- replacement items
- disputes
- dispute items
- refunds
- evidence

The exact final migration implementation governs details.

Return/replacement logistics depend on the original delivery method. Pickup transactions should not be given artificial shipment records.

## Finance

### Payments

Records payment transactions including subscriptions and invoice-related payments.

### Revenue

Records platform commission/revenue.

### Settlements

Records seller/provider amounts awaiting settlement.

### Payouts

Records successful payout batches. One payout can contain multiple settlements.

## Subscriptions

Subscriptions are part of the business model.

Plans can provide response allowances and other approved benefits.

Allocated responses can be stored on subscriptions and counted against usage during the subscription period.

Do not create unnecessary tables for benefits until a real feature requires them.

## Messaging

Messaging is shared across buying/selling/service roles.

Desktop may use a right-side drawer for recent conversations, with an option to open a full conversation page.

Mobile message access goes to the full message page rather than opening a drawer.

Do not duplicate message access in both bottom navigation and top navigation on mobile.

## Dashboard

The preferred navigation is a shared dashboard.

General/shared:

- Overview
- Subscription
- Messages
- Notifications
- Offers
- Invoices
- Shipments
- Locations
- Profile
- Help
- Logout

Buyer accordion:

- Favorites
- My Requests

Seller accordion:

- Items
- Listings
- Responses

A single user can use both buyer and seller functions.

## Layouts

### Marketplace layout

Used for public/storefront activity:

- Homepage
- Marketplace
- Listing details
- Public community
- Cart
- browsing/purchasing pages

### Dashboard layout

Used for authenticated account management:

- Overview
- Offers
- Invoices
- Shipments
- Locations
- Requests management
- Seller management
- Messages
- Notifications
- Profile
- Subscription

Do not move users into the dashboard simply because they are viewing a public marketplace resource.

## Mobile

Marketplace bottom navigation:

- Home
- Browse
- Community
- Cart
- Account

Authenticated message/notification access is handled at the top and navigates to their pages.

Guests should not see authenticated-only controls.

## Core workflows

### Normal cart purchase

Cart → Cart Items → Invoice → Invoice Items → Payment

### Cart negotiation

Cart → Offer → Offer Items → Accepted Offer → Invoice → Invoice Items → Payment

### Buyer-arranged delivery

Original transaction → Community delivery request → Responses → Offer → Shipment → Invoice → Payment

### Seller-arranged delivery

Product transaction → Offer if required → Shipment → Invoice → Payment

### Community product request

Discussion → Responses → Offers/counter offers → Accepted Offer → Shipment if required → Invoice → Payment

### Community service request

Discussion → Responses → Service Offer → Accepted Offer → Invoice → Payment → Service Job → Service Review

### Repair of a platform item

Item → Service request → Offer → Invoice → Payment → Service Job → Warranty

### Repair of an external item

Service request → Response → Offer → Invoice → Payment → Service Job with `item_id = null` and external item description

## Major domains/models

### Identity/account
- User
- Subscription Plan
- Subscription
- Location

### Marketplace
- Category
- Brand
- Model
- Item
- Component
- Listing
- Cart
- Cart Item
- Wishlist

### Community
- Discussion
- Response

### Negotiation
- Offer
- Offer Item

### Billing
- Invoice
- Invoice Item
- Payment

### Logistics
- Shipment
- Shipment Item

### Services
- Service Job
- Service Review

### Trust/resolution
- Issue
- Issue Item
- Return
- Return Item
- Replacement
- Replacement Item
- Dispute
- Dispute Item
- Refund
- Evidence

### Platform finance
- Revenue
- Settlement
- Payout
- Payout Settlement

## Architectural principles

1. Prefer the smallest model that accurately represents a real domain concept.
2. Do not duplicate data merely because two UI paths expose the same information.
3. Keep marketplace concepts separate from transaction records.
4. Keep offers as negotiation proposals.
5. Keep invoices as final commercial records.
6. Keep shipments as actual logistics records.
7. Keep service jobs as actual performed service records.
8. Persist agreed warranty information into final transaction/service records.
9. Use polymorphic relationships only where a record genuinely needs to reference multiple domain types.
10. Do not expose technical database terminology in customer-facing UI.
11. Do not introduce tables merely to satisfy navigation preferences.
12. One user can act in multiple roles.
13. Separate public visibility from private negotiation visibility.
14. Preserve historical transaction meaning.
15. Trace a complete user workflow before adding architecture.

## Important rejected directions

Do not:

- create `order_groups`
- create `wishlist_items`
- recreate redundant asset/inventory abstractions
- assume every offer item is a listing
- assume every service is a listing
- expose private offers publicly
- treat consignor/consignee as delivery responsibility
- require a shipment provider ID for the MVP
- build a full service marketplace/catalog before the core service transaction model proves the need

## Open questions

Resolve these deliberately rather than silently inventing answers:

1. Exact final migration set and migration ordering.
2. Exact state machines for offers, invoices, shipments and service jobs.
3. Exact final handling of accepted-offer history.
4. Exact final relationships between invoice items and shipment/service records.
5. Final issue/dispute architecture after service jobs.
6. Review aggregation rules.
7. Subscription benefits beyond response allocation.
8. Final mobile category-browser implementation.

## Development workflow

Before changing architecture:

1. Read this document.
2. Inspect current migrations/models/controllers/services.
3. Identify the relevant domain.
4. Trace the current workflow.
5. Explain conflicts between code and this context.
6. Propose the smallest change.
7. Implement only after the architecture is clear.
