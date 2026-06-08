# Triple T Shop — REST API Reference

**Base URL:** `http://shop.local:8080/?rest_route=`

> Note: Pretty permalinks (`/wp-json/`) are not enabled. Use `?rest_route=` for all requests.
> Write endpoints require authentication (WooCommerce API keys or WordPress cookie auth).

---

## Products

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products` | List all products / create a new product | `GET /?rest_route=/wc/v3/products&per_page=10` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/(id)` | Get, update, or delete a single product | `PUT /?rest_route=/wc/v3/products/58` → `{"regular_price":"19.99"}` |
| `POST` | `/wc/v3/products/batch` | Create, update, or delete multiple products at once | `POST` → `{"create":[{...}],"update":[{...}]}` |
| `GET` | `/wc/v3/products/(id)/related` | Get related products for a product | `GET /?rest_route=/wc/v3/products/58/related` → `[{id:31,name:"Platriple T"},...]` |
| `POST` | `/wc/v3/products/(id)/duplicate` | Duplicate an existing product | `POST /?rest_route=/wc/v3/products/58/duplicate` → new product `{id:143,...}` |
| `GET` | `/wc/v3/products/suggested-products` | Get product suggestions for search/autocomplete | `GET /?rest_route=/wc/v3/products/suggested-products` |
| `GET` | `/wc/v3/products/custom-fields/names` | List available custom field names | `GET /?rest_route=/wc/v3/products/custom-fields/names` |

### Product Categories

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/categories` | List or create product categories | `GET /?rest_route=/wc/v3/products/categories` → `[{id:15,name:"Kitchen & Dining"},...]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/categories/(id)` | Get, update, or delete a category | `POST` → `{"name":"Sahur Essentials","slug":"sahur-essentials"}` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/categories/batch` | Batch update categories | `POST` → `{"create":[{"name":"New Cat"}],"delete":[99]}` |

### Product Tags

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/tags` | List or create product tags | `GET /?rest_route=/wc/v3/products/tags` → `[{id:5,name:"sahur"},...]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/tags/(id)` | Get, update, or delete a tag | `POST` → `{"name":"ramadan","slug":"ramadan"}` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/tags/batch` | Batch update tags | `POST` → `{"create":[{"name":"hot"}]}` |

### Product Attributes

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/attributes` | List or create global attributes (e.g. Color, Size) | `GET /?rest_route=/wc/v3/products/attributes` → `[{id:1,name:"Color"},...]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/attributes/(id)` | Get, update, or delete an attribute | `POST` → `{"name":"Material","slug":"material","type":"select"}` |
| `GET` `POST` | `/wc/v3/products/attributes/(aid)/terms` | List or create terms for an attribute (e.g. Red, Blue) | `POST` → `{"name":"Cotton","slug":"cotton"}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/attributes/(aid)/terms/(id)` | Get, update, or delete a term | `DELETE /?rest_route=/wc/v3/products/attributes/2/terms/5` |

### Product Variations

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/(pid)/variations` | List or create variations (e.g. size M, size L) | `POST` → `{"regular_price":"29.99","attributes":[{"id":1,"option":"Large"}]}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/(pid)/variations/(id)` | Get, update, or delete a variation | `PATCH` → `{"stock_quantity":50}` |
| `POST` | `/wc/v3/products/(pid)/variations/generate` | Auto-generate all attribute combinations | `POST /?rest_route=/wc/v3/products/60/variations/generate` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/(pid)/variations/batch` | Batch update variations | `POST` → `{"create":[{...}],"update":[{...}]}` |

### Product Reviews

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/reviews` | List or create product reviews | `GET /?rest_route=/wc/v3/products/reviews?product=58` → `[{id:27,rating:5,review:"..."},...]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/reviews/(id)` | Get, update, or delete a review | `PUT` → `{"status":"approved"}` |

### Product Brands & Shipping Classes

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/products/brands` | List or create product brands | `POST` → `{"name":"TripleT","slug":"triple-t"}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/brands/(id)` | Get, update, or delete a brand | `PUT` → `{"name":"Tung Tung Tung"}` |
| `GET` `POST` | `/wc/v3/products/shipping_classes` | List or create shipping classes (e.g. Heavy, Fragile) | `GET /?rest_route=/wc/v3/products/shipping_classes` → `[{id:1,name:"Flat Rate"}]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/shipping_classes/(id)` | Get, update, or delete a shipping class | `POST` → `{"name":"Oversized","slug":"oversized"}` |
| `GET` | `/wc/v3/products/shipping_classes/slug-suggestion` | Suggest a slug from a name | `GET /?rest_route=/.../slug-suggestion?name=Express Delivery` |

---

## Orders

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/orders` | List all orders / create an order manually | `GET /?rest_route=/wc/v3/orders?status=processing` → `[{id:122,total:"33.98"},...]` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/orders/(id)` | Get, update, or delete a single order | `PUT` → `{"status":"completed"}` |
| `POST` `PUT` `PATCH` | `/wc/v3/orders/batch` | Batch update orders | `POST` → `{"update":[{"id":122,"status":"completed"}]}` |
| `GET` | `/wc/v3/orders/statuses` | List all available order statuses | `GET /?rest_route=/wc/v3/orders/statuses` → `{"pending":"Pending payment","processing":"Processing",...}` |
| `GET` `POST` | `/wc/v3/orders/(id)/notes` | List or add private order notes | `POST` → `{"note":"Customer called about delivery","customer_note":false}` |
| `GET` `DELETE` | `/wc/v3/orders/(oid)/notes/(id)` | Get or delete a specific note | `DELETE /?rest_route=/wc/v3/orders/122/notes/5` |
| `GET` `POST` | `/wc/v3/orders/(oid)/refunds` | List or create refunds | `POST` → `{"amount":"10.00","reason":"Damaged item"}` |
| `GET` `DELETE` | `/wc/v3/orders/(oid)/refunds/(id)` | Get or delete a refund | `DELETE /?rest_route=/wc/v3/orders/122/refunds/3` |
| `POST` | `/wc/v3/orders/(id)/actions/send_email` | Send order email to customer | `POST` → requests order details email |
| `GET` `POST` | `/wc/v3/orders/(id)/receipt` | Generate/view order receipt | `GET /?rest_route=/wc/v3/orders/122/receipt` |

---

## Customers

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/customers` | List all customers / create a customer | `GET /?rest_route=/wc/v3/customers?email=user@example.com` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/customers/(id)` | Get, update, or delete a customer | `PUT` → `{"first_name":"Ari","billing":{"phone":"+62..."}}` |
| `POST` `PUT` `PATCH` | `/wc/v3/customers/batch` | Batch update customers | `POST` → `{"update":[{"id":2,"email":"new@test.com"}]}` |
| `GET` | `/wc/v3/customers/(id)/downloads` | List downloadable files purchased by customer | `GET /?rest_route=/wc/v3/customers/2/downloads` |

---

## Coupons

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/coupons` | List all coupons / create a coupon | `POST` → `{"code":"SAHUR10","discount_type":"percent","amount":"10"}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/coupons/(id)` | Get, update, or delete a coupon | `PUT` → `{"amount":"15","expiry_date":"2026-12-31"}` |
| `POST` `PUT` `PATCH` | `/wc/v3/coupons/batch` | Batch update coupons | `POST` → `{"delete":[3,4,5]}` |

---

## Reports

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/reports` | List available report types | `GET /?rest_route=/wc/v3/reports` |
| `GET` | `/wc/v3/reports/sales` | Get sales report (date range, totals) | `GET /?rest_route=/wc/v3/reports/sales?date_min=2026-06-01&date_max=2026-06-30` |
| `GET` | `/wc/v3/reports/top_sellers` | Get top selling products | `GET /?rest_route=/wc/v3/reports/top_sellers?period=month` |
| `GET` | `/wc/v3/reports/orders/totals` | Order count by status | → `{"pending":2,"processing":5,"completed":12}` |
| `GET` | `/wc/v3/reports/products/totals` | Product count by type | → `{"simple":16,"variable":0}` |
| `GET` | `/wc/v3/reports/customers/totals` | Customer count | → `{"total":8,"paying":3}` |
| `GET` | `/wc/v3/reports/coupons/totals` | Coupon usage totals | → `{"total":1}` |
| `GET` | `/wc/v3/reports/reviews/totals` | Review count by status | → `{"approved":5,"hold":0}` |

---

## Settings

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/settings` | List all setting groups | `GET /?rest_route=/wc/v3/settings` → `[{id:"general",label:"General"},...]` |
| `GET` | `/wc/v3/settings/(group_id)` | Get settings for a group | `GET /?rest_route=/wc/v3/settings/general` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/settings/(group_id)/(id)` | Get or update a setting | `PUT` → `{"value":"USD"}` for currency |

---

## Shipping

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/shipping_methods` | List available shipping methods | `GET /?rest_route=/wc/v3/shipping_methods` → `[{id:"flat_rate",title:"Flat rate"},...]` |
| `GET` `POST` | `/wc/v3/shipping/zones` | List or create shipping zones | `POST` → `{"name":"Indonesia","order":1}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/shipping/zones/(id)` | Get, update, or delete a zone | `PUT` → `{"name":"Southeast Asia"}` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/shipping/zones/(id)/locations` | Manage zone locations (countries, states, postcodes) | `POST` → `{"code":"ID","type":"country"}` |
| `GET` `POST` | `/wc/v3/shipping/zones/(zid)/methods` | List or add methods to a zone | `POST` → `{"method_id":"flat_rate"}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/shipping/zones/(zid)/methods/(iid)` | Get, update, or delete a method instance | `PUT` → `{"settings":{"cost":"5.00"}}` |

---

## Taxes

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/taxes` | List or create tax rates | `POST` → `{"country":"ID","rate":"11","name":"PPN","class":"standard"}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/taxes/(id)` | Get, update, or delete a tax rate | `PUT` → `{"rate":"12"}` |
| `POST` `PUT` `PATCH` | `/wc/v3/taxes/batch` | Batch update tax rates | `POST` → `{"create":[{...}]}` |
| `GET` `POST` | `/wc/v3/taxes/classes` | List or create tax classes | `GET /?rest_route=/wc/v3/taxes/classes` → `[{slug:"standard",name:"Standard"},...]` |
| `GET` `DELETE` | `/wc/v3/taxes/classes/(slug)` | Get or delete a tax class | `DELETE /?rest_route=/wc/v3/taxes/classes/reduced-rate` |

---

## Payment Gateways

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/payment_gateways` | List all payment gateways | `GET /?rest_route=/wc/v3/payment_gateways` → `[{id:"bacs",title:"Direct bank transfer"},...]` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/payment_gateways/(id)` | Get or update gateway settings | `PUT` → `{"enabled":true,"title":"Bank Transfer"}` |

---

## Webhooks

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wc/v3/webhooks` | List or create webhooks (order.created, product.updated, etc.) | `POST` → `{"name":"Notify Slack","topic":"order.created","delivery_url":"https://..."}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/webhooks/(id)` | Get, update, or delete a webhook | `DELETE /?rest_route=/wc/v3/webhooks/3` |
| `POST` `PUT` `PATCH` | `/wc/v3/webhooks/batch` | Batch update webhooks | `POST` → `{"create":[{...}]}` |

---

## System

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/system_status` | Get system status report (WP version, PHP, DB, plugins) | `GET /?rest_route=/wc/v3/system_status` → `{"environment":{"wp_version":"7.0","php_version":"8.5.6"},...}` |
| `GET` | `/wc/v3/system_status/tools` | List available system tools | `GET /?rest_route=/wc/v3/system_status/tools` → `[{id:"clear_transients",name:"Clear transients"},...]` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/system_status/tools/(id)` | Get or run a system tool | `PUT` → run `clear_transients` |

---

## Data (Reference)

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/v3/data` | List all reference data types | `GET /?rest_route=/wc/v3/data` → `["continents","countries","currencies"]` |
| `GET` | `/wc/v3/data/continents` | List continents | → `[{code:"AS",name:"Asia"},{code:"EU",name:"Europe"},...]` |
| `GET` | `/wc/v3/data/countries` | List all countries with states | → `[{code:"ID",name:"Indonesia",states:[...]},{code:"US",...}]` |
| `GET` | `/wc/v3/data/currencies` | List all currencies | → `[{code:"USD",name:"US Dollar",symbol:"$"},{code:"IDR",...}]` |
| `GET` | `/wc/v3/data/currencies/current` | Get store base currency | → `{"code":"USD","name":"US Dollar","symbol":"$"}` |

---

## Store API (Public — no auth required)

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` | `/wc/store/v1/products` | Browse/search products (for front-end blocks) | `GET /?rest_route=/wc/store/v1/products` → `[{id:31,name:"Platriple T",prices:{price:"999",...}},...]` |
| `GET` | `/wc/store/v1/products/(id)` | Get a single product with prices, images, stock | → `{id:58,name:"Dry Tungwel",prices:{...},images:[...],is_in_stock:true}` |
| `GET` | `/wc/store/v1/cart` | Get current cart contents | → `{items:[{id:...,name:"...",quantity:1}],totals:{total_price:"3300"}}` |
| `POST` | `/wc/store/v1/cart/add-item` | Add item to cart | `POST` → `{"id":31,"quantity":2}` → `{"success":true}` |
| `POST` | `/wc/store/v1/cart/remove-item` | Remove item from cart | `POST` → `{"key":"c16a5320..."}` |
| `POST` | `/wc/store/v1/cart/update-item` | Update item quantity in cart | `POST` → `{"key":"c16a5320...","quantity":3}` |
| `POST` | `/wc/store/v1/checkout` | Process checkout (billing, shipping, payment) | `POST` → `{"billing_address":{...},"payment_method":"bacs"}` → `{order_id:122,...}` |

---

## WordPress Core

| Methods | Endpoint | Description | Example |
|---|---|---|---|
| `GET` `POST` | `/wp/v2/posts` | List or create blog posts | `GET /?rest_route=/wp/v2/posts?per_page=5` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/posts/(id)` | Get, update, or delete a post | `PUT` → `{"title":"Updated Post","status":"publish"}` |
| `GET` `POST` | `/wp/v2/pages` | List or create pages | `GET /?rest_route=/wp/v2/pages?slug=checkout` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/pages/(id)` | Get, update, or delete a page | `PUT` → `{"title":"About Us"}` |
| `GET` `POST` | `/wp/v2/media` | List or upload media files | `POST` with multipart upload → `{id:142,source_url:"http://..."}` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/media/(id)` | Get, update, or delete media | `DELETE /?rest_route=/wp/v2/media/142` |
| `GET` `POST` | `/wp/v2/comments` | List or create comments | `POST` → `{"post":31,"content":"Great product!","author_name":"Ari"}` |
| `GET` | `/wp/v2/users` | List users (requires auth for emails) | `GET /?rest_route=/wp/v2/users` → `[{id:1,name:"admin"},...]` |
| `GET` | `/wp/v2/categories` | List post categories | `GET /?rest_route=/wp/v2/categories` → `[{id:1,name:"Uncategorized"},...]` |
| `GET` | `/wp/v2/tags` | List post tags | `GET /?rest_route=/wp/v2/tags` → `[{id:1,name:"sahur"},...]` |
