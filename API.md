# Triple T Shop — REST API Reference

**Base URL:** `http://shop.local:8080/?rest_route=`

> Note: Pretty permalinks (`/wp-json/`) are not enabled. Use `?rest_route=` for all requests.
> Write endpoints require authentication (WooCommerce API keys or WordPress cookie auth).

---

## Products

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/(id)` |
| `POST` | `/wc/v3/products/batch` |
| `GET` | `/wc/v3/products/(id)/related` |
| `POST` | `/wc/v3/products/(id)/duplicate` |
| `GET` | `/wc/v3/products/suggested-products` |
| `GET` | `/wc/v3/products/custom-fields/names` |

### Product Categories

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/categories` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/categories/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/categories/batch` |

### Product Tags

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/tags` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/tags/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/tags/batch` |

### Product Attributes

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/attributes` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/attributes/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/attributes/batch` |
| `GET` `POST` | `/wc/v3/products/attributes/(attribute_id)/terms` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/attributes/(attribute_id)/terms/(id)` |

### Product Variations

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/(product_id)/variations` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/(product_id)/variations/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/products/(product_id)/variations/batch` |
| `POST` | `/wc/v3/products/(product_id)/variations/generate` |

### Product Reviews

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/reviews` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/reviews/(id)` |

### Product Brands & Shipping Classes

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/products/brands` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/brands/(id)` |
| `GET` `POST` | `/wc/v3/products/shipping_classes` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/products/shipping_classes/(id)` |
| `GET` | `/wc/v3/products/shipping_classes/slug-suggestion` |

---

## Orders

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/orders` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/orders/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/orders/batch` |
| `GET` | `/wc/v3/orders/statuses` |
| `GET` `POST` | `/wc/v3/orders/(id)/notes` |
| `GET` `DELETE` | `/wc/v3/orders/(order_id)/notes/(id)` |
| `GET` `POST` | `/wc/v3/orders/(order_id)/refunds` |
| `GET` `DELETE` | `/wc/v3/orders/(order_id)/refunds/(id)` |
| `GET` `POST` | `/wc/v3/orders/(id)/receipt` |
| `POST` | `/wc/v3/orders/(id)/actions/send_email` |
| `POST` | `/wc/v3/orders/(id)/actions/send_order_details` |
| `GET` | `/wc/v3/orders/(id)/actions/email_templates` |

---

## Customers

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/customers` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/customers/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/customers/batch` |
| `GET` | `/wc/v3/customers/(customer_id)/downloads` |

---

## Coupons

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/coupons` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/coupons/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/coupons/batch` |

---

## Reports

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/reports` |
| `GET` | `/wc/v3/reports/sales` |
| `GET` | `/wc/v3/reports/top_sellers` |
| `GET` | `/wc/v3/reports/orders/totals` |
| `GET` | `/wc/v3/reports/products/totals` |
| `GET` | `/wc/v3/reports/customers/totals` |
| `GET` | `/wc/v3/reports/coupons/totals` |
| `GET` | `/wc/v3/reports/reviews/totals` |

---

## Settings

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/settings` |
| `GET` | `/wc/v3/settings/(group_id)` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/settings/(group_id)/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/settings/batch` |

---

## Shipping

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/shipping_methods` |
| `GET` | `/wc/v3/shipping_methods/(id)` |
| `GET` `POST` | `/wc/v3/shipping/zones` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/shipping/zones/(id)` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/shipping/zones/(id)/locations` |
| `GET` `POST` | `/wc/v3/shipping/zones/(zone_id)/methods` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/shipping/zones/(zone_id)/methods/(instance_id)` |

---

## Taxes

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/taxes` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/taxes/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/taxes/batch` |
| `GET` `POST` | `/wc/v3/taxes/classes` |
| `GET` `DELETE` | `/wc/v3/taxes/classes/(slug)` |

---

## Payment Gateways

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/payment_gateways` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/payment_gateways/(id)` |

---

## Webhooks

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wc/v3/webhooks` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wc/v3/webhooks/(id)` |
| `POST` `PUT` `PATCH` | `/wc/v3/webhooks/batch` |

---

## System

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/system_status` |
| `GET` | `/wc/v3/system_status/tools` |
| `GET` `POST` `PUT` `PATCH` | `/wc/v3/system_status/tools/(id)` |

---

## Data (Reference)

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/v3/data` |
| `GET` | `/wc/v3/data/continents` |
| `GET` | `/wc/v3/data/countries` |
| `GET` | `/wc/v3/data/currencies` |
| `GET` | `/wc/v3/data/currencies/current` |

---

## Store API (Public — no auth required)

| Methods | Endpoint |
|---|---|
| `GET` | `/wc/store/v1/products` |
| `GET` | `/wc/store/v1/products/(id)` |
| `GET` `POST` | `/wc/store/v1/cart` |
| `GET` `POST` | `/wc/store/v1/cart/add-item` |
| `POST` | `/wc/store/v1/cart/remove-item` |
| `POST` | `/wc/store/v1/cart/update-item` |
| `POST` | `/wc/store/v1/checkout` |

---

## WordPress Core

| Methods | Endpoint |
|---|---|
| `GET` `POST` | `/wp/v2/posts` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/posts/(id)` |
| `GET` `POST` | `/wp/v2/pages` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/pages/(id)` |
| `GET` `POST` | `/wp/v2/media` |
| `GET` `POST` `PUT` `PATCH` `DELETE` | `/wp/v2/media/(id)` |
| `GET` `POST` | `/wp/v2/comments` |
| `GET` | `/wp/v2/users` |
| `GET` | `/wp/v2/categories` |
| `GET` | `/wp/v2/tags` |
