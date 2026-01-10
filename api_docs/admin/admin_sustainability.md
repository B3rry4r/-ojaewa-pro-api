# Admin Sustainability Management Endpoints

## Sustainability Initiative Management

### 1. Get All Sustainability Initiatives
**GET** `/api/admin/sustainability`
**Middleware:** `admin.auth`

**Query Parameters:**
- `category`: Filter by category (environmental/social/economic/governance)
- `status`: Filter by status (active/completed/planned/cancelled)
- `per_page`: Items per page (default: 10)

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiatives retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Eco-Friendly Packaging Initiative",
        "description": "Transition all sellers to biodegradable packaging materials to reduce environmental impact",
        "image_url": "https://example.com/images/eco-packaging.jpg",
        "category": "environmental",
        "status": "active",
        "target_amount": 1000000.00,
        "current_amount": 450000.00,
        "impact_metrics": "50% reduction in plastic waste, 200 sellers transitioned",
        "start_date": "2025-01-01",
        "end_date": "2025-12-31",
        "partners": ["EcoPackaging Ltd", "Green Solutions NG"],
        "participant_count": 200,
        "progress_notes": "On track to meet targets. Positive feedback from sellers.",
        "created_by": 1,
        "created_at": "2025-01-15T10:00:00.000000Z",
        "updated_at": "2025-01-20T14:00:00.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      },
      {
        "id": 2,
        "title": "Artisan Skills Development Program",
        "description": "Training program for traditional craftspeople to enhance their skills and market reach",
        "image_url": "https://example.com/images/artisan-training.jpg",
        "category": "social",
        "status": "active",
        "target_amount": 750000.00,
        "current_amount": 320000.00,
        "impact_metrics": "150 artisans trained, 80% increase in sales",
        "start_date": "2025-01-10",
        "end_date": "2025-06-30",
        "partners": ["Nigerian Artisan Guild", "Skills Development Foundation"],
        "participant_count": 150,
        "progress_notes": "Excellent response from participants. Expanding to more states.",
        "created_by": 1,
        "created_at": "2025-01-10T08:00:00.000000Z",
        "updated_at": "2025-01-20T16:00:00.000000Z",
        "admin": {
          "id": 1,
          "firstname": "Admin",
          "lastname": "User"
        }
      }
    ],
    "per_page": 10,
    "total": 8
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 2. Create New Sustainability Initiative
**POST** `/api/admin/sustainability`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "title": "Solar Power for Workshops",
  "description": "Install solar power systems in artisan workshops to reduce carbon footprint and energy costs",
  "image_url": "https://example.com/images/solar-workshop.jpg",
  "category": "environmental",
  "status": "planned",
  "target_amount": 2000000.00,
  "current_amount": 0,
  "impact_metrics": "Expected 70% reduction in electricity costs, 50 workshops powered",
  "start_date": "2025-03-01",
  "end_date": "2025-08-31",
  "partners": ["Solar Energy Solutions", "Green Tech Nigeria"],
  "participant_count": 50,
  "progress_notes": "Initial planning phase. Conducting feasibility studies."
}
```

**Validation Rules:**
- `title`: required|string|max:255
- `description`: required|string
- `image_url`: nullable|url
- `category`: required|in:environmental,social,economic,governance
- `status`: required|in:active,completed,planned,cancelled
- `target_amount`: nullable|numeric|min:0
- `current_amount`: nullable|numeric|min:0
- `impact_metrics`: nullable|string
- `start_date`: nullable|date
- `end_date`: nullable|date|after_or_equal:start_date
- `partners`: nullable|array
- `participant_count`: nullable|integer|min:0
- `progress_notes`: nullable|string

**Response (201):**
```json
{
  "status": "success",
  "message": "Sustainability initiative created successfully",
  "data": {
    "id": 3,
    "title": "Solar Power for Workshops",
    "description": "Install solar power systems in artisan workshops to reduce carbon footprint and energy costs",
    "image_url": "https://example.com/images/solar-workshop.jpg",
    "category": "environmental",
    "status": "planned",
    "target_amount": 2000000.00,
    "current_amount": 0,
    "impact_metrics": "Expected 70% reduction in electricity costs, 50 workshops powered",
    "start_date": "2025-03-01",
    "end_date": "2025-08-31",
    "partners": ["Solar Energy Solutions", "Green Tech Nigeria"],
    "participant_count": 50,
    "progress_notes": "Initial planning phase. Conducting feasibility studies.",
    "created_by": 1,
    "created_at": "2025-01-20T18:00:00.000000Z",
    "updated_at": "2025-01-20T18:00:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 3. Update Sustainability Initiative
**PUT** `/api/admin/sustainability/{sustainabilityInitiative}`
**Middleware:** `admin.auth`

**Request:**
```json
{
  "status": "active",
  "current_amount": 600000.00,
  "participant_count": 250,
  "progress_notes": "Exceeding targets! Expanded program to include more artisans.",
  "impact_metrics": "60% reduction in plastic waste, 250 sellers transitioned, 15% cost savings"
}
```

**Validation Rules:**
- `title`: sometimes|string|max:255
- `description`: sometimes|string
- `image_url`: nullable|url
- `category`: sometimes|in:environmental,social,economic,governance
- `status`: sometimes|in:active,completed,planned,cancelled
- `target_amount`: nullable|numeric|min:0
- `current_amount`: nullable|numeric|min:0
- `impact_metrics`: nullable|string
- `start_date`: nullable|date
- `end_date`: nullable|date|after_or_equal:start_date
- `partners`: nullable|array
- `participant_count`: nullable|integer|min:0
- `progress_notes`: nullable|string

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiative updated successfully",
  "data": {
    "id": 1,
    "title": "Eco-Friendly Packaging Initiative",
    "status": "active",
    "current_amount": 600000.00,
    "participant_count": 250,
    "progress_notes": "Exceeding targets! Expanded program to include more artisans.",
    "impact_metrics": "60% reduction in plastic waste, 250 sellers transitioned, 15% cost savings",
    "updated_at": "2025-01-20T19:00:00.000000Z",
    "admin": {
      "id": 1,
      "firstname": "Admin",
      "lastname": "User"
    }
  }
}
```

**Test Coverage:** 🔴 Not Tested

---

### 4. Delete Sustainability Initiative
**DELETE** `/api/admin/sustainability/{sustainabilityInitiative}`
**Middleware:** `admin.auth`

**Request:** No body required

**Response (200):**
```json
{
  "status": "success",
  "message": "Sustainability initiative deleted successfully"
}
```

**Test Coverage:** 🔴 Not Tested

---

## Sustainability Categories

### Environmental Initiatives:
- **Carbon Footprint Reduction** - Renewable energy, efficient logistics
- **Waste Management** - Packaging reduction, recycling programs
- **Sustainable Materials** - Eco-friendly fabrics, biodegradable packaging
- **Water Conservation** - Efficient dyeing processes, water recycling

### Social Initiatives:
- **Artisan Development** - Skills training, fair wage programs
- **Community Support** - Local sourcing, community workshops
- **Education Programs** - Financial literacy, digital skills training
- **Gender Equality** - Women empowerment, equal opportunity programs

### Economic Initiatives:
- **Fair Trade Practices** - Transparent pricing, direct trade relationships
- **Local Economy Support** - Sourcing from local suppliers
- **Financial Inclusion** - Microfinance programs, payment solutions
- **Supply Chain Optimization** - Efficient distribution, cost reduction

### Governance Initiatives:
- **Transparency** - Open reporting, stakeholder engagement
- **Ethical Standards** - Code of conduct, compliance programs
- **Accountability** - Regular audits, impact measurement
- **Stakeholder Engagement** - Community involvement, feedback systems

## Initiative Status Flow

### Status Progression:
1. **planned** - Initiative in planning phase
2. **active** - Currently running initiative
3. **completed** - Successfully finished initiative
4. **cancelled** - Initiative cancelled or discontinued

## Impact Tracking

### Key Metrics:
- **Financial Impact** - Target vs. current funding amounts
- **Participation** - Number of participants/beneficiaries
- **Environmental Metrics** - Carbon reduction, waste reduction
- **Social Metrics** - Lives impacted, skills developed
- **Progress Tracking** - Regular updates and milestone tracking

## Test Coverage Summary

- **Sustainability Management:** 🔴 0/4 endpoints tested (0%)

**Total Sustainability Management:** 🔴 0/4 endpoints tested (0%)

---
*Last Updated: January 2025*