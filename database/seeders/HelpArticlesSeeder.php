<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HelpArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@partsandparcel.com')->first()
            ?? User::whereNotNull('role_id')->first()
            ?? User::first();

        if (!$author) {
            $this->call(DemoUsersSeeder::class);
            $author = User::first();
        }

        $helpArticles = [
            // 1. Buying & Making Offers
            [
                'title' => 'How to Negotiate and Counter-Offer on Parts',
                'topic' => 'Buying & Making Offers',
                'tags' => 'Buying & Making Offers, negotiation, counter offers, discounts, checkout',
                'excerpt' => 'Learn how to submit price proposals, review counter-offers from sellers, and finalize purchases with locked pricing.',
                'content' => <<<EOT
## Overview

Parts & Parcel allows verified buyers to negotiate unit prices directly with sellers on eligible listings. Negotiating allows you to secure package discounts for multi-item repair bundles or commercial spare requirements.

### Step 1: Submitting an Offer
1. On any eligible item page or marketplace quick view, click the **"Make an Offer"** button.
2. Enter your proposed unit price, quantity, and optional notes explaining your target pricing.
3. Review the calculated escrow total and submit. The seller will immediately receive an alert on their dashboard and registered email.

### Step 2: Reviewing Counter-Offers
If the seller is unable to accept your initial offer, they may propose a counter-offer. You will be notified in real-time.
- Open your **Offers** tab in your buyer dashboard or click the quick notification drawer.
- You can accept the counter-offer, propose a revised price, or decline the offer.
- **Important**: Offers are time-limited (standard expiration is 72 hours). Once expired, inventory releases back to the public pool.

### Step 3: Fast-Track Checkout
Once both parties reach an agreement, click **"Proceed to Escrow Checkout"**. Your agreed price is locked, preventing other shoppers from purchasing the allocated units while you complete the transaction.
EOT
            ],
            [
                'title' => 'Direct Marketplace Offers vs Public RFQs',
                'topic' => 'Buying & Making Offers',
                'tags' => 'Buying & Making Offers, rfq, direct offers, buying guide',
                'excerpt' => 'Understand when to make a direct offer on a seller listing versus broadcasting an open community request to all verified vendors.',
                'content' => <<<EOT
## Choosing the Right Buying Channel

Depending on the rarity of the component you need, you have two primary procurement routes on Parts & Parcel:

### 1. Direct Listing Offers
Use direct offers when an exact item or donor vehicle part is already cataloged on the marketplace:
- **Fastest turnaround**: Deal one-on-one with the stocking merchant.
- **Exact specs**: Review detailed condition photos, OEM part numbers, and testing warranties before offering.
- **Instant escrow locking**: Immediate path to checkout once terms are agreed upon.

### 2. Community Requests (RFQs)
Use Community RFQs when the part is scarce, obsolete, out of stock, or requires specialist yard salvage:
- **Broadcast to hundreds of yards**: Auto mechanics, electronics dismantlers, and industrial suppliers across all hub cities receive your request.
- **Competitive bidding**: Multiple vendors submit quotes with photos and warranties, allowing you to choose the best price and location.
EOT
            ],

            // 2. Escrow & Secure Payments
            [
                'title' => 'Understanding the 48-Hour Escrow Protection Window',
                'topic' => 'Escrow & Secure Payments',
                'tags' => 'Escrow & Secure Payments, escrow protection, buyer safety, inspection period',
                'excerpt' => 'Discover how our buyer-seller escrow system keeps your money safe until you inspect and test your delivered item.',
                'content' => <<<EOT
## How Parts & Parcel Escrow Safeguards Your Funds

Unlike informal classifieds where payments are sent directly to unknown bank accounts, Parts & Parcel holds 100% of purchase funds in a secure neutral escrow account until delivery and inspection are finalized.

### The Escrow Lifecycle

```
[Payment Confirmed] -> [Seller Ships Waybill] -> [Parcel Delivered] -> [48-Hour Inspection Window] -> [Funds Disbursed to Seller]
```

### The Inspection Window
- Once your tracking shows the item has arrived or your courier delivers the package, the **48-Hour Inspection Clock** begins.
- During this period, thoroughly examine the part:
  - Check for transit physical damage or cracks.
  - Verify OEM serial and model stampings against listing photos.
  - For electronic or mechanical units (ECUs, alternators, motherboards), install and benchmark immediately.

### Releasing Funds vs Opening a Dispute
- **Satisfied**: Click **"Confirm & Release Escrow"** in your dashboard to immediately release payout to the seller.
- **Issue detected**: If the part is non-functional or not as described, click **"Open Dispute"** *before* the 48-hour timer lapses. Escrow payout is instantly frozen pending mediation.
EOT
            ],
            [
                'title' => 'Payment Methods, Multi-Currency Invoicing & Receipt Verification',
                'topic' => 'Escrow & Secure Payments',
                'tags' => 'Escrow & Secure Payments, payment methods, cards, transfers, invoices',
                'excerpt' => 'A guide to accepted payment gateways, bank transfers, automatic receipts, and currency localization.',
                'content' => <<<EOT
## Accepted Payment Methods

We support several high-security payment channels:
- **Debit / Credit Cards**: Visa, Mastercard, and Verve processed via PCI-DSS Level 1 compliant gateways.
- **Direct Bank Transfer / Virtual Accounts**: Dynamic dedicated accounts generated for frictionless bank app transfers.
- **Platform Wallet Balance**: Use existing earnings or refunded escrow credits for instantaneous settlement.

### Multi-Currency & Regional Conversion
Parts & Parcel automatically presents prices in your localized currency based on your active location settings. Currency exchange calculations for international transactions are refreshed daily at interbank reference rates.

### Downloadable VAT Invoices & Receipts
Every completed escrow deposit generates a compliant digital PDF invoice and receipt accessible under your **Dashboard > Orders & Invoices** tab.
EOT
            ],

            // 3. Shipping & Deliveries
            [
                'title' => 'Tracking Waybills & Inspecting Delivered Parcels',
                'topic' => 'Shipping & Deliveries',
                'tags' => 'Shipping & Deliveries, waybill tracking, courier, delivery, parcel inspection',
                'excerpt' => 'How to monitor transit milestones, coordinate with local logistics partners, and safely receive your parcel.',
                'content' => <<<EOT
## Tracking Your Shipment

Once a seller packages your order, they generate a platform-tracked waybill with one of our integrated logistics partners (or submit verifiable third-party courier dispatch details).

### Finding Your Tracking Number
1. Head to **Dashboard > Orders**.
2. Select your order to view the live tracking timeline.
3. You will see dispatch status, dispatch hub, transit checkpoints, and expected delivery date.

### Best Practices When Receiving Deliveries
- **Inspect packaging before the rider leaves**: If the external carton is crushed, torn, or leaking fluids, photograph the exterior immediately.
- **Unboxing video**: For high-value assemblies (engines, transmissions, high-end laptops), we strongly advise recording a continuous unboxing video.
- **Keep dispatch waybills**: Retain the physical shipping sticker attached to the box until the 48-hour inspection period concludes.
EOT
            ],
            [
                'title' => 'What to Do If an Item Arrives Damaged or Defective',
                'topic' => 'Shipping & Deliveries',
                'tags' => 'Shipping & Deliveries, damaged goods, transit claims, returns',
                'excerpt' => 'Step-by-step instructions on documenting parcel damage and claiming transit insurance or refunds.',
                'content' => <<<EOT
## Handling Transit Damage

Even with heavy-duty packaging, heavy machinery spares and delicate electronics can occasionally suffer courier mishandling. Here is how to protect your investment:

1. **Take Clear Photos**: Take close-up and wide-angle pictures showing the shipping label, packaging condition, and the damage on the part itself.
2. **Do Not Alter or Modify the Item**: Do not attempt invasive repairs or disassembly if you plan to return the item.
3. **Notify Support Immediately**: Go to your order page and click **"Report Shipping Damage"** or open a dispute within the 48-hour inspection window.
4. **Return Logistics**: Our team will provide a pre-authorized return waybill or courier instructions to return the item to the vendor hub or inspection depot.
EOT
            ],

            // 4. Seller Tiers & Plans
            [
                'title' => 'Vendor Membership Privileges, Listing Limits & Escrow Caps',
                'topic' => 'Seller Tiers & Plans',
                'tags' => 'Seller Tiers & Plans, seller subscription, vendor tiers, listing limits, commission',
                'excerpt' => 'Compare starter, verified pro, and enterprise seller tiers to find the right growth plan for your parts business.',
                'content' => <<<EOT
## Seller Tier Comparison

Parts & Parcel offers tiered seller memberships tailored to independent dismantling yards, regional refurbishers, and high-volume spare parts dealerships.

### Key Tier Benefits
- **Basic / Free Tier**:
  - Perfect for occasional sellers parting out single project vehicles or surplus inventory.
  - Up to 10 active listings with standard marketplace search placement.
  - Standard escrow disbursement (48 hours post-delivery).
- **Pro Merchant**:
  - Designed for established spare parts shops and repair garages.
  - Unlimited active listings and bulk CSV inventory uploads.
  - Reduced platform commission fee.
  - Priority badge on product listings and search results.
- **Enterprise / Salvage Yard**:
  - Tailored for large dismantling yards and certified auto recyclers.
  - Dedicated account manager and custom API integration for inventory syncing.
  - Expedited 24-hour escrow settlements and zero monthly listing caps.

### How to Upgrade or Switch Plans
Visit **Dashboard > Subscription Plans** (or visit the public **/pricing** page) to select your preferred monthly or annual billing cycle.
EOT
            ],
            [
                'title' => 'How to Upgrade Your Seller Tier and Access Wholesale Tools',
                'topic' => 'Seller Tiers & Plans',
                'tags' => 'Seller Tiers & Plans, billing, upgrades, wholesale inventory, seller tools',
                'excerpt' => 'Learn how to activate premium vendor perks, bulk price updates, and multi-branch inventory management.',
                'content' => <<<EOT
## Activating Your Seller Upgrade

Upgrading your vendor membership takes less than two minutes:

1. Log into your seller dashboard.
2. Navigate to **Subscription & Billing**.
3. Review the available plans tailored to your registered business country.
4. Select your billing tenure (Annual subscriptions include a discount of up to 20%).
5. Complete the checkout using card or wallet balance. Your plan benefits unlock instantaneously.
EOT
            ],

            // 5. Community Requests (RFQs)
            [
                'title' => 'Posting Community Requests & Comparing Mechanic Quotes',
                'topic' => 'Community Requests (RFQs)',
                'tags' => 'Community Requests (RFQs), rfq, buyer quotes, salvage requests, mechanic help',
                'excerpt' => 'How to broadcast requests for hard-to-find car or gadget parts and evaluate competitive offers from verified merchants.',
                'content' => <<<EOT
## How Community RFQs Work

Can't find the exact engine harness, gearbox sensor, or laptop logic board you need on the public catalog? Community Requests connect you directly with hundreds of dismantling yards and specialized technicians.

### Submitting a Request
1. Click **"Post a Request"** on the marketplace header.
2. Select your category, target brand, and exact model year.
3. Upload photos of the old part or VIN chassis plate to ensure precision fitment.
4. Set your target budget and delivery city.

### Reviewing Vendor Bids
- Verified vendors receive real-time notifications matching their registered categories.
- Sellers respond with photos of the physical part in stock, their quote, and delivery lead time.
- You can message vendors directly to clarify specifications.
- Once you find the right match, click **"Accept Quote"** to instantly convert the response into a protected escrow checkout!
EOT
            ],
            [
                'title' => 'Tips for Responding to Buyer RFQs with Competitive Bids',
                'topic' => 'Community Requests (RFQs)',
                'tags' => 'Community Requests (RFQs), seller quotes, winning bids, vendor tips',
                'excerpt' => 'A vendor guide to writing winning quotes, providing accurate part condition photos, and winning more deals.',
                'content' => <<<EOT
## Winning More RFQ Bids as a Seller

Buyers choose vendors who provide transparency and prompt communication. Follow these tips to improve your bid conversion rate:

- **Attach Clear Photos of the Actual Item**: Avoid generic stock photos. Snap the part currently on your shelf or donor vehicle.
- **Highlight Testing Warranty**: Mention whether the item has been bench-tested or comes with a replacement warranty.
- **State Accurate Delivery Estimates**: Specify whether you have the item ready for same-day dispatch or require 24 hours to pull it from the vehicle.
- **Price Transparently**: Include all necessary accessories (e.g. including pulleys with an alternator or plugs with an ECU).
EOT
            ],

            // 6. Disputes & Mediation
            [
                'title' => 'How to File a Dispute Before Escrow Releases',
                'topic' => 'Disputes & Mediation',
                'tags' => 'Disputes & Mediation, dispute resolution, buyer protection, return refund, mediation',
                'excerpt' => 'Everything you need to know about opening a formal claim, pausing escrow disbursements, and submitting proof.',
                'content' => <<<EOT
## When to Open a Dispute

You should file a dispute if any of the following occur during the 48-hour post-delivery window:
- The item received is physically defective, burnt, or non-functional.
- The item does not match the specifications, year, or OEM number agreed upon in the listing/offer.
- The package arrived empty, severely tampered with, or items were missing from a multi-item bundle.

### Step-by-Step Dispute Process
1. Navigate to **Dashboard > Orders** and open the active order.
2. Click **"Open Dispute / Request Refund"**.
3. Choose the primary reason (e.g., *Defective Component*, *Wrong Item Received*, *Transit Damage*).
4. Provide a detailed summary and attach photos or video evidence.
5. Submit the dispute. **Escrow payout to the seller is immediately frozen**.

Neither party can cancel or claim the funds until the dispute is formally resolved by the parties or our mediation desk.
EOT
            ],
            [
                'title' => 'The Mediation Process: Evidence Submission and Resolution Timeline',
                'topic' => 'Disputes & Mediation',
                'tags' => 'Disputes & Mediation, mediation timeline, dispute escalation, fair resolution',
                'excerpt' => 'A breakdown of our 3-stage dispute resolution process: direct party discussion, admin mediation, and refund execution.',
                'content' => <<<EOT
## The 3-Stage Resolution Workflow

Our goal is always fair and transparent mediation based on physical evidence and transaction records.

### Stage 1: Mutual Agreement (24 Hours)
Once a dispute is logged, buyer and seller have 24 hours to reach a direct compromise (for example: seller offers a partial discount refund, or ships a verified replacement component immediately).

### Stage 2: Admin Mediation Desk (24-48 Hours)
If both parties cannot agree, the case automatically escalates to a Parts & Parcel Senior Arbitrator.
- Our team examines listing photos, chat logs, waybill transit weight, and buyer-submitted diagnostic proofs.
- If necessary, an independent certified technician or partner hub is assigned to inspect the part.

### Stage 3: Resolution & Settlement
The arbitrator issues a binding decision:
- **Full Refund**: Item is returned to seller via trackable waybill; buyer receives 100% refund of escrow funds.
- **Partial Refund**: Buyer keeps item and receives an agreed adjustment amount; balance is released to seller.
- **Escrow Release to Seller**: If claim is determined to be false or buyer alters the unit improperly.
EOT
            ],
        ];

        foreach ($helpArticles as $articleData) {
            $slug = Str::slug($articleData['title']);

            Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => null,
                    'is_help' => true,
                    'title' => $articleData['title'],
                    'excerpt' => $articleData['excerpt'],
                    'content' => $articleData['content'],
                    'tags' => $articleData['tags'],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 15)),
                ]
            );
        }
    }
}
