# Parts & Parcel — AI Development Rules

1. Read `docs/PROJECT_CONTEXT.md` before making architectural or database changes.
2. Inspect the actual current code and migrations before changing them.
3. Treat current code as implementation reality; flag discrepancies rather than silently redesigning.

6. Do not recreate redundant asset/inventory abstractions.
7. Do not create tables merely to make navigation easier.
8. Preserve the established Parts & Parcel terminology.
9. One user can buy, sell, respond, provide services and provide delivery.
10. Offers are negotiation proposals; invoices are final commercial records.
11. Offer discount is at offer level.
12. Invoice discount is at invoice level.
13. Offer items may refer to listings or describe non-listing commercial items such as services or delivery.
14. Shipments represent actual movement of goods.
15. Service jobs represent actual performed services.
16. Persist agreed warranty terms into the final transaction/service record.
17. Public community content and private offers have different visibility rules.
18. Marketplace pages use marketplace layout; account-management pages use dashboard layout.
19. Desktop drawers may be used contextually; mobile messaging navigates to a full page.
20. Before adding a table, first determine whether an existing model can accurately represent the requirement.
21. Before modifying a relationship, trace all affected workflows.
22. For every feature identify: domain, models, relationships, records created, records updated, lifecycle, visibility and completion/cancellation behavior.
23. Avoid speculative abstractions and unrelated scope creep.
24. Do not expose `assetable`, `polymorphic`, `inventory item` or similar implementation terms in customer-facing UI.
25. Prefer explicit, understandable Laravel migrations/models over clever abstractions.
26. Check foreign keys, nullability, delete behavior, indexes, uniqueness, state transitions, authorization and race conditions before finalizing database changes.
