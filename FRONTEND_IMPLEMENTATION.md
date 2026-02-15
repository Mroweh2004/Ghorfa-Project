# Frontend Implementation - Transaction Workflow

## Overview
This document describes the frontend implementation of the complete transaction workflow for rental and purchase requests in the Ghorfa platform.

---

## User Flow: Requesting a Property (Buyer/Tenant)

### 1. **Property Show Page**
- **Location**: `/properties/{property}` route displays `resources/views/show.blade.php`
- **Request Section**: New section "Interested in this property?" appears if user is not the landlord and not admin
- **Buttons Available**:
  - **For Rental Properties**: "Request to Rent" button
  - **For Sale Properties**: "Request to Buy" button

### 2. **Request Modal**
When user clicks request button, a modal opens with form fields:

**For Rental Requests**:
- Start Date (required)
- End Date (required, must be after start date)
- Rules Accepted (checkbox)
- Rules Exceptions (optional textarea - what rules user doesn't accept)
- Additional Notes (optional textarea)

**For Purchase Requests**:
- Additional Notes (optional textarea)

### 3. **Submit Request**
- Form submits to `POST /transactions/` (TransactionController@store)
- Creates new Transaction with status = `pending`
- Returns JSON response with success/error message
- Displays success alert and reloads page

---

## Landlord Dashboard Flow

### 1. **Dashboard Navigation**
The landlord dashboard (`/landlord/dashboard`) has been updated with new sidebar items:
- **Rental/Purchase Requests** - Shows count of pending requests
- **Active Transactions** - Shows count of ongoing transactions

### 2. **Requests Section**

#### Table Columns:
- Buyer (name, email, avatar)
- Property (title)
- Type (Rental/Purchase badge)
- Status (Pending Review)
- Request Date
- Actions (Review button)

#### Review Action:
- Opens modal showing full request details
- Displays:
  - Buyer information
  - Property details
  - Check-in/Check-out dates (for rentals)
  - Rules acceptance status
  - Buyer's notes/concerns
  - CTA buttons:
    - "Generate Contract & Send" - Creates and sends contract to buyer
    - "Download Report" - Downloads transaction report as HTML

### 3. **Active Transactions Section**

#### Table Columns:
- Buyer (name, avatar)
- Property (title)
- Type (Rental/Purchase badge)
- Status (Awaiting Payment / Payment Received)
- Amount (price with currency)
- Actions (context-specific buttons)

#### Status-Specific Actions:
- **Confirmed (Awaiting Payment)**:
  - "Confirm Payment" - Marks payment as received
  
- **Paid (Payment Received)**:
  - "Complete" - Marks transaction as completed
  - "Refund" - Initiates refund process (shows prompt for reason)

---

## Workflow Diagrams

### Rental Request Workflow (User Side)
```
Property Page
    ↓
Click "Request to Rent" button
    ↓
Modal Opens (form)
    ↓
Fill dates & rules info
    ↓
Submit Request
    ↓
Success Alert
    ↓
[Waiting for Landlord Review]
```

### Landlord Review Process
```
Landlord Dashboard
    ↓
See pending request in "Requests" section
    ↓
Click "Review" button
    ↓
Modal Shows Request Details
    ↓
Option A: Generate Contract & Send
    ├→ Contract created
    ├→ Sent to buyer
    ├→ Status changes to "pending" (awaiting buyer approval)
    
Option B: Download Report
    └→ HTML report opens/downloads
```

### Active Transaction Management
```
After buyer approves contract:
    Appears in "Active Transactions"
    Status: "Awaiting Payment"
    ↓
Landlord confirms payment received
    ↓
Status: "Payment Received"
    ↓
Landlord can:
  A) Complete transaction → Status: "Completed"
  B) Request refund → Initiates refund workflow
```

---

## Frontend Files Modified/Created

### Views
1. **resources/views/show.blade.php**
   - Added transaction request section
   - Added two modals (rental & purchase request)
   - Added JavaScript functions for form submission
   - Conditionally shows buttons based on listing type

2. **resources/views/landlord/dashboard.blade.php**
   - Added transaction requests section
   - Added active transactions section
   - Updated sidebar navigation with new items
   - Added modal for request details
   - Added JavaScript functions for transaction actions

### CSS Files
1. **public/css/transaction-request.css**
   - Modal overlay styles
   - Form styling
   - Request card styling
   - Transaction section styling
   - Responsive design

2. **public/css/landlord-tables.css**
   - Request/Transaction table styling
   - Status badge colors
   - Action button styling
   - Avatar styling
   - Responsive table behavior

### Controllers Updated
1. **app/Http/Controllers/TransactionController.php**
   - Added `downloadReport()` method - generates HTML report
   - Added helper methods for report generation:
     - `generateTransactionReport()`
     - `getTransactionTypeLabel()`
     - `getStatusLabel()`
     - `getRoomDetails()`
     - `getRentalDates()`
     - `getRulesAndNotes()`
     - `getTimeline()`

2. **app/Http/Controllers/LandlordController.php**
   - Updated `dashboard()` method to fetch:
     - Transaction requests (pending, no contract)
     - Active transactions (confirmed/paid)
   - Added stats for request/transaction counts

### Routes Added
1. **GET** `/transactions/{transaction}/download-report`
   - Downloads transaction report as HTML
   - Can be printed or saved as PDF browser feature

---

## Components & Features

### Request/Transaction Modals
- Overlay-based modal with backdrop click to close
- ESC key to close
- Form validation on client side
- AJAX submission with CSRF protection
- Success/error message handling

### Data Display
- Request details formatted in grid layout
- Timeline of transaction events
- Property information summary
- Buyer/Landlord information display

### Transaction Report
- Professional HTML layout
- Print-friendly styling
- Includes all transaction details:
  - Transaction ID and dates
  - Property information
  - Buyer/Landlord details
  - Rental period (for rentals)
  - Rules and notes
  - Timeline of events
  - Proper formatting and styling

---

## API Endpoints Used (Frontend)

### Creating Requests
- **POST** `/transactions/` - Create new transaction request

### Viewing Details
- **GET** `/transactions/{id}` - Get transaction details (JSON)
- **GET** `/transactions/{id}/download-report` - Get HTML report

### Landlord Actions
- **POST** `/transactions/{id}/generate-contract` - Generate contract
- **POST** `/transactions/{id}/confirm-payment` - Confirm payment received
- **POST** `/transactions/{id}/complete` - Complete transaction
- **POST** `/transactions/{id}/request-refund` - Request refund

---

## User Experience Enhancements

### Feedback
- Success/error alerts after form submission
- Modal loading states
- Button disabled states
- Form validation messages

### Accessibility
- Semantic HTML elements
- ARIA labels where applicable
- Keyboard navigation (ESC to close modals)
- Responsive design for mobile/tablet

### Visual Feedback
- Status badges with color coding:
  - Yellow: Pending
  - Blue: Confirmed
  - Green: Paid/Completed
  - Red: Cancelled/Failed
- Transaction type badges
- Avatar display for users

---

## Next Steps / Future Enhancements

1. **PDF Generation**: Currently HTML report. Could integrate PDF library (e.g., DomPDF, mPDF)
2. **Email Notifications**: Send emails when:
   - Contract is generated and sent
   - Buyer approves/rejects contract
   - Payment status changes
3. **Document Storage**: Store contracts in storage/app/contracts/
4. **Digital Signatures**: Add signature verification for contracts
5. **Payment Gateway Integration**: Link to actual payment processing
6. **Refund Management**: Track refund status and completion
7. **Dispute Resolution**: Add messages/chat between parties

---

## Testing Checklist

- [ ] User can see "Request to Rent/Buy" button on property page
- [ ] Modal opens with correct form fields
- [ ] Form submits successfully
- [ ] Landlord sees request in dashboard
- [ ] Can click Review to see full details
- [ ] Can generate contract
- [ ] Can download transaction report
- [ ] Payment confirmation works
- [ ] Complete transaction works
- [ ] Refund request works
- [ ] Responsive design works on mobile
- [ ] Modal closes with ESC key
- [ ] Modal closes with X button
- [ ] All navigation links work correctly

---

## CSS Classes Reference

### Modal Classes
- `.modal-overlay` - Background overlay
- `.modal-content` - Modal container
- `.modal-header` - Header section
- `.modal-close` - Close button
- `.modal-actions` - Action buttons container

### Form Classes
- `.form-group` - Form field container
- `.checkbox-group` - Checkbox container
- `.checkbox-label` - Checkbox label

### Table Classes
- `.requests-table-wrapper` - Request table wrapper
- `.transactions-table-wrapper` - Transaction table wrapper
- `.request-buyer-cell` - Buyer info cell
- `.request-actions` - Action buttons cell

### Badge Classes
- `.status-pending` - Pending status badge
- `.status-confirmed` - Confirmed status badge
- `.status-paid` - Paid status badge
- `.badge-primary` - Primary badge color
- `.badge-success` - Success badge color

---

## Notes

- All forms use CSRF protection
- Modal state is managed purely with CSS display property
- No external modal library used (vanilla CSS/JS)
- Responsive design implemented with CSS media queries
- Transaction report includes print-friendly styling (@media print)
