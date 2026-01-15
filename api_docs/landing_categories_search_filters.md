# Landing Categories + Search + Filters API (Client Guide)

This document describes how the mobile client should build the Landing Page category navigation (Textiles, Afro Beauty, Shoes & Bags, Art) and how to use the **Search** and **Filters** endpoints in a way that returns **different results for each category/subcategory**.

> Important: The API uses globally-unique category slugs. Always use the `slug` values returned from the API.

---

## Table of Contents
1. [Category System Overview](#category-system-overview)
2. [Category Endpoints](#category-endpoints)
3. [Landing Page Taxonomy (What Exists)](#landing-page-taxonomy-what-exists)
4. [Fetching Items for a Category/Subcategory](#fetching-items-for-a-categorysubcategory)
5. [Products: Browse / Search / Filters](#products-browse--search--filters)
6. [Businesses (Services): Public Search / Filters](#businesses-services-public-search--filters)
7. [Sustainability: Index / Search / Filters](#sustainability-index--search--filters)
8. [Recommended Client Flow (End-to-End)](#recommended-client-flow-end-to-end)

---

## Category System Overview

The backend stores hierarchical categories in the `categories` table.

### Category Types
These are the only valid `type` values for categories:
- `textiles` (Landing Box 1)
- `afro_beauty` (Landing Box 2)
- `shoes_bags` (Landing Box 3)
- `art` (Landing Box 4)
- `school` (services/businesses)
- `sustainability` (initiatives)

### What each type returns
When the client calls the category-items endpoint (see below), the API returns:

| Category type | Returned data |
|---|---|
| `textiles` | **Products** |
| `shoes_bags` | **Products** |
| `art` | **Products** |
| `afro_beauty` | **Products** for product subtree, **Businesses** for services subtree |
| `school` | **Businesses** |
| `sustainability` | **Sustainability initiatives** |

---

## Category Endpoints

### 1) List Categories by Type

**GET** `/api/categories?type={type}`

**Query params**
- `type` (required): `textiles | afro_beauty | shoes_bags | art | school | sustainability`

**Example**
```bash
curl "https://<host>/api/categories?type=textiles"
```

**Response (example)**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "For Women",
      "slug": "textiles-women",
      "type": "textiles",
      "children": [
        {
          "id": 10,
          "name": "Categories",
          "slug": "textiles-women-categories",
          "children": [
            { "id": 20, "name": "Dresses & Gowns", "slug": "textiles-women-categories-dresses-gowns" }
          ]
        }
      ]
    }
  ]
}
```

---

### 2) Fetch Items for a Category/Subcategory

**GET** `/api/categories/{type}/{slug}/items`

**Path params**
- `type`: one of the category types
- `slug`: the slug returned from `/api/categories?type=...`

**Query params**
- `per_page` (optional)

**Example**
```bash
curl "https://<host>/api/categories/textiles/textiles-women-categories-dresses-gowns/items?per_page=10"
```

**Response**
- For product types: returns paginated **products**
- For business types: returns paginated **business profiles**
- For sustainability type: returns paginated **initiatives**

---

## Landing Page Taxonomy (What Exists)

### Box 1: TEXTILES (`type=textiles`)
- For Women → Categories → Dresses & Gowns, Two-Piece Sets, Wrappers & Skirts, Tops, Headwear & Accessories, Outerwear, Special Occasion
- For Men → Categories → Full Suits & Gowns, Two-Piece Sets, Shirts & Tops, Trousers, Wrap Garments, Outerwear, Accessories
- Unisex / For Both → Categories → Modern Casual Wear, Capes & Stoles, Home & Lounge Wear, Accessories
- Filter by Fabrics → Ankara, Kente, Adinkra, Aso Oke, …

### Box 2: AFRO BEAUTY (`type=afro_beauty`)
- Categories Under Products → Hair Care, Skin Care, Makeup & Color Cosmetics, Fragrance, Men’s Grooming, Wellness & Bath/Body, Children’s Afro-Beauty, Tools & Accessories
- Categories Under Services → Hair Care & Styling Services, Skin Care & Aesthetics Services, Makeup Artistry Services, Barbering Services, Education & Consulting Services, Wellness & Therapeutic Services

> Note: In categories, the Services subtree uses slugs starting with `afro-beauty-services...`. The items endpoint will return **business profiles** for that subtree.

### Box 3: SHOES & BAGS (`type=shoes_bags`)
- For Women → Slides & Mules, Block Heel Sandals & Pumps, Wedges, Ballet Flats & Loafers, Evening & Wedding Shoes
- For Men → African Print Slip-Ons & Loafers, Leather Sandals, Modern Māṣǝr, Brogues & Derbies

### Box 4: ART (`type=art`)
- Sculpture, Painting, Mask, Mixed Media, Installation

---

## Fetching Items for a Category/Subcategory

### Textiles example
```bash
GET /api/categories/textiles/textiles-men-categories-shirts-tops/items
```
Returns **Products**.

### Afro Beauty products example
```bash
GET /api/categories/afro_beauty/afro-beauty-products-hair-care/items
```
Returns **Products**.

### Afro Beauty services example
```bash
GET /api/categories/afro_beauty/afro-beauty-services-hair-care-styling-services/items
```
Returns **Business Profiles**.

### Shoes & Bags example
```bash
GET /api/categories/shoes_bags/shoes-bags-women-categories-slides-mules/items
```
Returns **Products**.

### Art example
```bash
GET /api/categories/art/art-sculpture/items
```
Returns **Products**.

---

## Products: Browse / Search / Filters

### 1) Browse Products (Public)
**GET** `/api/products/browse`

Supports:
- `q` (search term)
- `sort` (`newest|popular|price_asc|price_desc`)
- `price_min`, `price_max`
- `gender`, `style`, `tribe`

> Note: browse is generic. For landing categories, prefer `/api/categories/{type}/{slug}/items`.

---

### 2) Search Products
**GET** `/api/products/search`

**Query params**
- `q` (required)
- `type` (optional): `textiles|afro_beauty|shoes_bags|art`
- `category_id` (optional)
- `category_slug` (optional)
- plus existing filters: `gender, style, tribe, price_min, price_max, per_page`

**Example: search within Textiles**
```bash
GET /api/products/search?q=ankara&type=textiles
```

**Example: search within a category subtree**
```bash
GET /api/products/search?q=dress&category_slug=textiles-women
```

---

### 3) Product Filters Metadata
**GET** `/api/products/filters`

Returns:
- `category_trees` for: textiles, afro_beauty, shoes_bags, art
- plus generic filters: genders/styles/tribes/price_range/sort_options

---

## Businesses (Services): Public Search / Filters

### 1) Public Business Listing
**GET** `/api/business/public`

### 2) Search Businesses
**GET** `/api/business/public/search`

**Query params**
- `q` (required)
- legacy filters: `category` (school|afro_beauty), `offering_type`, `state`, `city`
- new filters (recommended):
  - `category_id`
  - `category_slug`

**Example: search Afro Beauty service providers under services subtree**
```bash
GET /api/business/public/search?q=hair&category_slug=afro-beauty-services
```

### 3) Business Filters
**GET** `/api/business/public/filters`

Returns:
- `category_trees.school`
- `category_trees.afro_beauty_services`
- plus states/cities/offering_types

---

## Sustainability: Index / Search / Filters

### 1) List initiatives
**GET** `/api/sustainability`

Supports:
- legacy `category` (environmental/social/economic/governance)
- new (recommended): `category_id`, `category_slug`

### 2) Search initiatives
**GET** `/api/sustainability/search`

Supports:
- `q` (required)
- legacy `category`
- new: `category_id`, `category_slug`

### 3) Sustainability Filters
**GET** `/api/sustainability/filters`

Returns:
- legacy `categories`
- new `category_tree` from categories table

---

## Recommended Client Flow (End-to-End)

### Landing page → category navigation
1. Load categories for a landing box
   - Textiles: `GET /api/categories?type=textiles`
   - Afro Beauty: `GET /api/categories?type=afro_beauty`
   - Shoes & Bags: `GET /api/categories?type=shoes_bags`
   - Art: `GET /api/categories?type=art`

2. Render the tree using `children`.

3. When user taps any node (including “View all”), call:
   - `GET /api/categories/{type}/{slug}/items`

Because the backend filters by that category **and all descendants**, “View all” works naturally by using the parent node slug.

### Search
- Use `/api/products/search` for product landing pages
- Use `/api/business/public/search` for service landing pages

### Filters
- Use `/api/products/filters` and `/api/business/public/filters` to pre-load filter options.

---

## Notes / Constraints
- `slug` is globally unique. Do not guess slugs.
- `category_slug` filtering works on **subtrees** (category + descendants).
- For Afro Beauty, the services subtree is identified by slugs starting with `afro-beauty-services...`.
