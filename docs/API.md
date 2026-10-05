# Admission Portal API Documentation

**Version:** 1.0.0  
**Base URL:** `http://localhost:8000`  
**Authentication:** Laravel Sanctum (Bearer Token)  
**Content-Type:** application/json

---

## Table of Contents

1. [Authentication](#1-authentication)
2. [Public API](#2-public-api)
3. [Student Application](#3-student-application)
4. [Payments](#4-payments)
5. [M-Pesa Webhooks](#5-m-pesa-webhooks)
6. [Offers](#6-offers)
7. [Profile](#7-profile)
8. [Messages](#8-messages)
9. [FAQs](#9-faqs)
10. [Notifications](#10-notifications)
11. [Admin Dashboard](#11-admin-dashboard)
12. [Admin Applications](#12-admin-applications)
13. [Admin Admission Letters](#13-admin-admission-letters)
14. [Admin Programs](#14-admin-programs)
15. [Admin Intakes](#15-admin-intakes)
16. [Admin Users](#16-admin-users)
17. [Admin Roles & Permissions](#17-admin-roles--permissions)
18. [Admin Payments](#18-admin-payments)
19. [Admin Documents](#19-admin-documents)
20. [Admin Broadcasts](#20-admin-broadcasts)
21. [Admin Form Fields](#21-admin-form-fields)
22. [Admin FAQs](#22-admin-faqs)
23. [Admin Settings](#23-admin-settings)
24. [Admin AI Settings](#24-admin-ai-settings)
25. [Admin AI Conversations](#25-admin-ai-conversations)
26. [Admin AI Leads](#26-admin-ai-leads)
27. [Admin Messages](#27-admin-messages)
28. [Admin Inquiries](#28-admin-inquiries)
29. [Admin Subscriptions](#29-admin-subscriptions)
30. [Admin Profile](#30-admin-profile)
31. [Admin Notifications](#31-admin-notifications)
32. [Admin Letter Templates](#32-admin-letter-templates)
33. [Admin Audit Logs](#33-admin-audit-logs)
34. [Super Admin Schools](#34-super-admin-schools)
35. [Super Admin Programs](#35-super-admin-programs)
36. [Super Admin Backups](#36-super-admin-backups)
37. [Super Admin Activity Logs](#37-super-admin-activity-logs)
38. [Super Admin Reports](#38-super-admin-reports)
39. [AI Endpoints](#39-ai-endpoints)
40. [Sessions](#40-sessions)
41. [Language](#41-language)

---

## 1. Authentication

### Login
- **Endpoint:** `POST /login`
- **Description:** Authenticate user
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "student@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "student@example.com"
  },
  "token": "eyJpdiI6Ij..."
}
```

### Register
- **Endpoint:** `POST /register`
- **Description:** Register new student
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "student@example.com",
    "phone": "+254700000000",
    "password": "password",
    "password_confirmation": "password"
  }'
```

### Logout
- **Endpoint:** `POST /logout`
- **Description:** Logout user
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/logout \
  -H "Authorization: Bearer {token}"
```

### Forgot Password
- **Endpoint:** `POST /forgot-password`
- **Description:** Send password reset link
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "student@example.com"}'
```

### Reset Password
- **Endpoint:** `POST /reset-password`
- **Description:** Reset password
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "token": "reset-token",
    "password": "newpassword",
    "password_confirmation": "newpassword"
  }'
```

### Verify Email
- **Endpoint:** `GET /email/verify/{id}/{hash}`
- **Description:** Verify user email
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/email/verify/1/{hash}
```

---

## 2. Public API

### Check Program Eligibility
- **Endpoint:** `POST /api/program/eligibility`
- **Description:** Check if user meets program eligibility criteria
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/api/program/eligibility \
  -H "Content-Type: application/json" \
  -d '{
    "program_id": 1,
    "grade": "A",
    "year": 2024
  }'
```

**Response:**
```json
{
  "eligible": true,
  "message": "You meet the eligibility criteria",
  "requirements_met": ["grade", "year"],
  "requirements_not_met": []
}
```

### Get Program Requirements
- **Endpoint:** `GET /api/program/{programId}/requirements`
- **Description:** Get program requirements
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/api/program/1/requirements
```

**Response:**
```json
{
  "program_id": 1,
  "requirements": [
    {"id": 1, "name": "KCSE Mean Grade", "value": "C+"},
    {"id": 2, "name": "English", "value": "C"}
  ]
}
```

### Get Authenticated User
- **Endpoint:** `GET /api/user`
- **Description:** Get current authenticated user
- **Auth:** Required (Sanctum)

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "student@example.com",
  "phone": "+254700000000",
  "school_id": 1
}
```

---

## 3. Student Application

### Student Dashboard
- **Endpoint:** `GET /student/dashboard`
- **Description:** View student dashboard
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/dashboard \
  -H "Authorization: Bearer {token}"
```

### Create Application
- **Endpoint:** `GET /student/application/create`
- **Description:** Show application create form
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/application/create \
  -H "Authorization: Bearer {token}"
```

### Store Application
- **Endpoint:** `POST /student/application`
- **Description:** Store new application
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/application \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "program_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "student@example.com",
    "phone": "+254700000000",
    "gender": "male",
    "date_of_birth": "2000-01-01",
    "id_number": "12345678"
  }'
```

### View Application
- **Endpoint:** `GET /student/application/{application}`
- **Description:** View application details
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/application/1 \
  -H "Authorization: Bearer {token}"
```

### Save Form Step
- **Endpoint:** `POST /student/application/save/{step}`
- **Description:** Save application form step
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/application/save/personal \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"first_name": "John"}'
```

### Submit Application
- **Endpoint:** `POST /student/application/submit/{application}`
- **Description:** Submit application
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/application/submit/1 \
  -H "Authorization: Bearer {token}"
```

### Review Application
- **Endpoint:** `GET /student/application/review/{application}`
- **Description:** Review application before submission
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/application/review/1 \
  -H "Authorization: Bearer {token}"
```

### Show Application Form Step
- **Endpoint:** `GET /student/application/form/{step}`
- **Description:** Show application form step
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/application/form/personal \
  -H "Authorization: Bearer {token}"
```

### Download Application PDF
- **Endpoint:** `GET /student/application/{application}/pdf`
- **Description:** Download application as PDF
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/application/1/pdf \
  -H "Authorization: Bearer {token}"
```

---

## 4. Payments

### Show Payment Page
- **Endpoint:** `GET /student/payment/{application}`
- **Description:** Show payment page
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/payment/1 \
  -H "Authorization: Bearer {token}"
```

### Initiate Payment
- **Endpoint:** `POST /student/payment/{application}/initiate`
- **Description:** Initiate payment
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/payment/1/initiate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"payment_method": "mpesa"}'
```

### Initiate M-Pesa
- **Endpoint:** `POST /student/payment/initiate-mpesa`
- **Description:** Initiate M-Pesa payment
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/payment/initiate-mpesa \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "application_id": 1,
    "phone": "254700000000"
  }'
```

### Submit Manual Payment
- **Endpoint:** `POST /student/payment/{application}/manual`
- **Description:** Submit manual payment proof
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/payment/1/manual \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: multipart/form-data" \
  -F "receipt_file=@receipt.pdf"
```

### Check Payment Status
- **Endpoint:** `GET /student/payment/{application}/status`
- **Description:** Check payment status
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/payment/1/status \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "payment_id": 1,
  "status": "completed",
  "amount": 2000,
  "created_at": "2024-01-01T00:00:00Z"
}
```

### Download Receipt
- **Endpoint:** `GET /student/payment/{payment}/receipt`
- **Description:** Download payment receipt
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/payment/1/receipt \
  -H "Authorization: Bearer {token}"
```

### Payment Callback
- **Endpoint:** `POST /student/payment/{application}/callback`
- **Description:** Payment callback handler
- **Auth:** Not Required

---

## 5. M-Pesa Webhooks

### M-Pesa Callback
- **Endpoint:** `POST /mpesa/callback`
- **Description:** M-Pesa payment callback
- **Auth:** Not Required

### M-Pesa Confirmation
- **Endpoint:** `POST /mpesa/confirmation`
- **Description:** Payment confirmation
- **Auth:** Not Required

### M-Pesa Validation
- **Endpoint:** `POST /mpesa/validation`
- **Description:** Payment validation
- **Auth:** Not Required

### M-Pesa Result
- **Endpoint:** `GET /mpesa/result`
- **Description:** Payment result
- **Auth:** Not Required

---

## 6. Offers

### List Offers
- **Endpoint:** `GET /student/offers`
- **Description:** List all offers
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/offers \
  -H "Authorization: Bearer {token}"
```

### View Offer
- **Endpoint:** `GET /student/offers/{offer}`
- **Description:** View offer details
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/offers/1 \
  -H "Authorization: Bearer {token}"
```

### Accept Offer
- **Endpoint:** `POST /student/offers/{offer}/accept`
- **Description:** Accept offer
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/offers/1/accept \
  -H "Authorization: Bearer {token}"
```

### Decline Offer
- **Endpoint:** `POST /student/offers/{offer}/decline`
- **Description:** Decline offer
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/offers/1/decline \
  -H "Authorization: Bearer {token}"
```

### Download Offer Letter
- **Endpoint:** `GET /student/offers/{offer}/download`
- **Description:** Download offer letter
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/offers/1/download \
  -H "Authorization: Bearer {token}"
```

### Preview Offer
- **Endpoint:** `GET /student/offers/{offer}/preview`
- **Description:** Preview offer
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/offers/1/preview \
  -H "Authorization: Bearer {token}"
```

---

## 7. Profile

### Edit Profile
- **Endpoint:** `GET /student/profile`
- **Description:** Edit profile form
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/profile \
  -H "Authorization: Bearer {token}"
```

### Update Profile
- **Endpoint:** `PUT /student/profile`
- **Description:** Update profile
- **Auth:** Required

```bash
curl -X PUT http://localhost:8000/student/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe"
  }'
```

### Toggle Dark Mode
- **Endpoint:** `POST /student/profile/dark-mode`
- **Description:** Toggle dark mode
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/profile/dark-mode \
  -H "Authorization: Bearer {token}"
```

### Update Password
- **Endpoint:** `PUT /student/profile/password`
- **Description:** Update password
- **Auth:** Required

```bash
curl -X PUT http://localhost:8000/student/profile/password \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
  }'
```

### Student Settings
- **Endpoint:** `GET /student/settings`
- **Description:** Get settings
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/settings \
  -H "Authorization: Bearer {token}"
```

### Update Settings
- **Endpoint:** `PUT /student/settings`
- **Description:** Update settings
- **Auth:** Required

```bash
curl -X PUT http://localhost:8000/student/settings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"language": "en"}'
```

---

## 8. Messages

### List Conversations
- **Endpoint:** `GET /student/messages`
- **Description:** List conversations
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/messages \
  -H "Authorization: Bearer {token}"
```

### Create Conversation
- **Endpoint:** `POST /student/messages`
- **Description:** Create conversation
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/messages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2, "message": "Hello"}'
```

### View Conversation
- **Endpoint:** `GET /student/messages/{conversation}`
- **Description:** View conversation
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/messages/1 \
  -H "Authorization: Bearer {token}"
```

### Send Message
- **Endpoint:** `POST /student/messages/{conversation}/messages`
- **Description:** Send message
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/messages/1/messages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello"}'
```

### Start Conversation
- **Endpoint:** `POST /student/messages/start`
- **Description:** Start new conversation
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/messages/start \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```

### Get Unread Count
- **Endpoint:** `GET /student/messages/unread-count`
- **Description:** Get unread message count
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/messages/unread-count \
  -H "Authorization: Bearer {token}"
```

### Mark as Read
- **Endpoint:** `POST /student/messages/{conversation}/read`
- **Description:** Mark conversation as read
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/messages/1/read \
  -H "Authorization: Bearer {token}"
```

### Typing
- **Endpoint:** `POST /student/messages/{conversation}/typing`
- **Description:** Send typing indicator
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/messages/1/typing \
  -H "Authorization: Bearer {token}"
```

---

## 9. FAQs

### List FAQs
- **Endpoint:** `GET /student/faqs`
- **Description:** List FAQs
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/student/faqs
```

---

## 10. Notifications

### List Notifications
- **Endpoint:** `GET /student/notifications`
- **Description:** List notifications
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/student/notifications \
  -H "Authorization: Bearer {token}"
```

### Mark All Read
- **Endpoint:** `POST /student/notifications/mark-all`
- **Description:** Mark all notifications as read
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/notifications/mark-all \
  -H "Authorization: Bearer {token}"
```

### Mark Read
- **Endpoint:** `POST /student/notifications/{id}/read`
- **Description:** Mark notification as read
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/student/notifications/1/read \
  -H "Authorization: Bearer {token}"
```

---

## 11. Admin Dashboard

### Dashboard
- **Endpoint:** `GET /admin/dashboard`
- **Description:** Admin dashboard
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/dashboard \
  -H "Authorization: Bearer {token}"
```

### Analytics
- **Endpoint:** `GET /admin/analytics`
- **Description:** Analytics data
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/analytics \
  -H "Authorization: Bearer {token}"
```

### Accountant Dashboard
- **Endpoint:** `GET /admin/accountant`
- **Description:** Accountant dashboard
- **Auth:** Required (Accountant)

```bash
curl -X GET http://localhost:8000/admin/accountant \
  -H "Authorization: Bearer {token}"
```

### Accountant Payments
- **Endpoint:** `GET /admin/accountant/payments`
- **Description:** Accountant payments
- **Auth:** Required (Accountant)

```bash
curl -X GET http://localhost:8000/admin/accountant/payments \
  -H "Authorization: Bearer {token}"
```

### Accountant Export
- **Endpoint:** `GET /admin/accountant/export`
- **Description:** Export payments
- **Auth:** Required (Accountant)

```bash
curl -X GET http://localhost:8000/admin/accountant/export \
  -H "Authorization: Bearer {token}"
```

### Reviewer Dashboard
- **Endpoint:** `GET /admin/reviewer`
- **Description:** Reviewer dashboard
- **Auth:** Required (Reviewer)

```bash
curl -X GET http://localhost:8000/admin/reviewer \
  -H "Authorization: Bearer {token}"
```

### Reviewer Applications
- **Endpoint:** `GET /admin/reviewer/applications`
- **Description:** Reviewer applications
- **Auth:** Required (Reviewer)

```bash
curl -X GET http://localhost:8000/admin/reviewer/applications \
  -H "Authorization: Bearer {token}"
```

---

## 12. Admin Applications

### List Applications
- **Endpoint:** `GET /admin/applications`
- **Description:** List applications
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/applications \
  -H "Authorization: Bearer {token}"
```

### View Application
- **Endpoint:** `GET /admin/applications/{application}`
- **Description:** View application
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/applications/1 \
  -H "Authorization: Bearer {token}"
```

### Approve Application
- **Endpoint:** `POST /admin/applications/{application}/approve`
- **Description:** Approve application
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/applications/1/approve \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"notes": "Approved"}'
```

### Reject Application
- **Endpoint:** `POST /admin/applications/{application}/reject`
- **Description:** Reject application
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/applications/1/reject \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason": "Does not meet criteria"}'
```

### Request Info
- **Endpoint:** `POST /admin/applications/{application}/request-info`
- **Description:** Request more information
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/applications/1/request-info \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"message": "Please provide more documents"}'
```

### Export PDF
- **Endpoint:** `GET /admin/applications/{application}/pdf`
- **Description:** Export application PDF
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/applications/1/pdf \
  -H "Authorization: Bearer {token}"
```

### Bulk Action
- **Endpoint:** `POST /admin/applications/bulk-action`
- **Description:** Perform bulk action
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/applications/bulk-action \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "action": "approve",
    "application_ids": [1, 2, 3]
  }'
```

### Export Applications
- **Endpoint:** `GET /admin/applications/export`
- **Description:** Export applications
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/applications/export \
  -H "Authorization: Bearer {token}"
```

### Add Note
- **Endpoint:** `POST /admin/reviewer/applications/{application}/note`
- **Description:** Add note to application
- **Auth:** Required (Reviewer)

```bash
curl -X POST http://localhost:8000/admin/reviewer/applications/1/note \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"note": "Reviewed"}'
```

### Start Review
- **Endpoint:** `POST /admin/reviewer/applications/{application}/start`
- **Description:** Start application review
- **Auth:** Required (Reviewer)

```bash
curl -X POST http://localhost:8000/admin/reviewer/applications/1/start \
  -H "Authorization: Bearer {token}"
```

---

## 13. Admin Admission Letters

### List Letters
- **Endpoint:** `GET /admin/admission-letters`
- **Description:** List admission letters
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/admission-letters \
  -H "Authorization: Bearer {token}"
```

### Create Letter
- **Endpoint:** `POST /admin/admission-letters`
- **Description:** Create admission letter
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/admission-letters \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "application_id": 1,
    "template_id": 1
  }'
```

### Bulk Create
- **Endpoint:** `GET /admin/admission-letters/bulk`
- **Description:** Bulk create letters
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/admission-letters/bulk \
  -H "Authorization: Bearer {token}"
```

### Bulk Store
- **Endpoint:** `POST /admin/admission-letters/bulk`
- **Description:** Bulk store letters
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/admission-letters/bulk \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"application_ids": [1, 2, 3]}'
```

### Download Letter
- **Endpoint:** `GET /admin/admission-letters/{letter}/download`
- **Description:** Download letter
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/admission-letters/1/download \
  -H "Authorization: Bearer {token}"
```

### Resend Letter
- **Endpoint:** `POST /admin/admission-letters/{letter}/resend`
- **Description:** Resend letter
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/admission-letters/1/resend \
  -H "Authorization: Bearer {token}"
```

---

## 14. Admin Programs

### List Programs
- **Endpoint:** `GET /admin/programs`
- **Description:** List programs
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/programs \
  -H "Authorization: Bearer {token}"
```

### Create Program
- **Endpoint:** `POST /admin/programs`
- **Description:** Create program
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/programs \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Computer Science",
    "code": "CS",
    "description": "Computer Science Program"
  }'
```

### Update Program
- **Endpoint:** `PUT /admin/programs/{program}`
- **Description:** Update program
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/programs/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Computer Science"}'
```

### Delete Program
- **Endpoint:** `DELETE /admin/programs/{program}`
- **Description:** Delete program
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/programs/1 \
  -H "Authorization: Bearer {token}"
```

### Toggle Status
- **Endpoint:** `POST /admin/programs/{program}/toggle-status`
- **Description:** Toggle program status
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/programs/1/toggle-status \
  -H "Authorization: Bearer {token}"
```

### Add Global Program
- **Endpoint:** `POST /admin/programs/add-global`
- **Description:** Add global program
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/programs/add-global \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"global_program_id": 1}'
```

---

## 15. Admin Intakes

### List Intakes
- **Endpoint:** `GET /admin/intakes`
- **Description:** List intakes
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/intakes \
  -H "Authorization: Bearer {token}"
```

### Create Intake
- **Endpoint:** `POST /admin/intakes`
- **Description:** Create intake
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/intakes \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "January 2024",
    "start_date": "2024-01-01",
    "end_date": "2024-03-31"
  }'
```

### Update Intake
- **Endpoint:** `PUT /admin/intakes/{intake}`
- **Description:** Update intake
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/intakes/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "January 2024"}'
```

### Delete Intake
- **Endpoint:** `DELETE /admin/intakes/{intake}`
- **Description:** Delete intake
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/intakes/1 \
  -H "Authorization: Bearer {token}"
```

### Set Current
- **Endpoint:** `POST /admin/intakes/{intake}/set-current`
- **Description:** Set as current intake
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/intakes/1/set-current \
  -H "Authorization: Bearer {token}"
```

---

## 16. Admin Users

### List Users
- **Endpoint:** `GET /admin/users`
- **Description:** List users
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/users \
  -H "Authorization: Bearer {token}"
```

### Update User
- **Endpoint:** `PUT /admin/users/{user}`
- **Description:** Update user
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"first_name": "John"}'
```

### Reset Password
- **Endpoint:** `POST /admin/users/{user}/reset-password`
- **Description:** Reset user password
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/users/1/reset-password \
  -H "Authorization: Bearer {token}"
```

---

## 17. Admin Roles & Permissions

### List Roles
- **Endpoint:** `GET /admin/roles`
- **Description:** List roles
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/roles \
  -H "Authorization: Bearer {token}"
```

### Create Role
- **Endpoint:** `POST /admin/roles`
- **Description:** Create role
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/roles \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit_posts", "delete_posts"]
  }'
```

### Update Role
- **Endpoint:** `PUT /admin/roles/{role}`
- **Description:** Update role
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/roles/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "editor"}'
```

### Delete Role
- **Endpoint:** `DELETE /admin/roles/{role}`
- **Description:** Delete role
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/roles/1 \
  -H "Authorization: Bearer {token}"
```

### Permissions
- **Endpoint:** `GET /admin/roles/permissions`
- **Description:** List permissions
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/roles/permissions \
  -H "Authorization: Bearer {token}"
```

### Assign Role
- **Endpoint:** `POST /admin/roles/assign/{user}`
- **Description:** Assign role to user
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/roles/assign/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"role": "admin"}'
```

### Revoke Role
- **Endpoint:** `DELETE /admin/roles/assign/{user}`
- **Description:** Revoke role from user
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/roles/assign/1 \
  -H "Authorization: Bearer {token}"
```

### Bulk Assign
- **Endpoint:** `POST /admin/roles/assign-bulk`
- **Description:** Bulk assign roles
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/roles/assign-bulk \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_ids": [1, 2, 3],
    "role": "admin"
  }'
```

---

## 18. Admin Payments

### View Payment
- **Endpoint:** `GET /admin/payments/{payment}`
- **Description:** View payment
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/payments/1 \
  -H "Authorization: Bearer {token}"
```

### Verify Payment
- **Endpoint:** `POST /admin/payments/{payment}/verify`
- **Description:** Verify payment
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/payments/1/verify \
  -H "Authorization: Bearer {token}"
```

### Reject Payment
- **Endpoint:** `POST /admin/payments/{payment}/reject`
- **Description:** Reject payment
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/payments/1/reject \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason": "Invalid receipt"}'
```

### Download Receipt
- **Endpoint:** `GET /admin/payments/{payment}/receipt`
- **Description:** Download receipt
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/payments/1/receipt \
  -H "Authorization: Bearer {token}"
```

---

## 19. Admin Documents

### List Documents
- **Endpoint:** `GET /admin/documents`
- **Description:** List documents
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/documents \
  -H "Authorization: Bearer {token}"
```

### Verify Document
- **Endpoint:** `POST /admin/documents/{document}/verify`
- **Description:** Verify document
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/documents/1/verify \
  -H "Authorization: Bearer {token}"
```

### Reject Document
- **Endpoint:** `POST /admin/documents/{document}/reject`
- **Description:** Reject document
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/documents/1/reject \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason": "Document unreadable"}'
```

### Bulk Verify
- **Endpoint:** `POST /admin/documents/bulk-verify`
- **Description:** Bulk verify documents
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/documents/bulk-verify \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"document_ids": [1, 2, 3]}'
```

---

## 20. Admin Broadcasts

### List Broadcasts
- **Endpoint:** `GET /admin/broadcast`
- **Description:** List broadcasts
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/broadcast \
  -H "Authorization: Bearer {token}"
```

### Create Broadcast
- **Endpoint:** `POST /admin/broadcast`
- **Description:** Create broadcast
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/broadcast \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Announcement",
    "message": "Important announcement",
    "recipients": "all"
  }'
```

### List Templates
- **Endpoint:** `GET /admin/broadcast/templates`
- **Description:** List broadcast templates
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/broadcast/templates \
  -H "Authorization: Bearer {token}"
```

### Store Template
- **Endpoint:** `POST /admin/broadcast/templates`
- **Description:** Store template
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/broadcast/templates \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Welcome",
    "content": "Welcome message"
  }'
```

### Update Template
- **Endpoint:** `PUT /admin/broadcast/templates/{template}`
- **Description:** Update template
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/broadcast/templates/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"content": "Updated content"}'
```

### Cancel Broadcast
- **Endpoint:** `POST /admin/broadcast/{broadcast}/cancel`
- **Description:** Cancel broadcast
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/broadcast/1/cancel \
  -H "Authorization: Bearer {token}"
```

### Resend Broadcast
- **Endpoint:** `POST /admin/broadcast/{broadcast}/resend`
- **Description:** Resend broadcast
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/broadcast/1/resend \
  -H "Authorization: Bearer {token}"
```

---

## 21. Admin Form Fields

### List Form Fields
- **Endpoint:** `GET /admin/form-fields`
- **Description:** List form fields
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/form-fields \
  -H "Authorization: Bearer {token}"
```

### Create Section
- **Endpoint:** `POST /admin/form-fields/sections`
- **Description:** Create section
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/sections \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Personal Details", "order": 1}'
```

### Update Section
- **Endpoint:** `PUT /admin/form-fields/sections/{section}`
- **Description:** Update section
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/form-fields/sections/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Personal Details"}'
```

### Delete Section
- **Endpoint:** `DELETE /admin/form-fields/sections/{section}`
- **Description:** Delete section
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/form-fields/sections/1 \
  -H "Authorization: Bearer {token}"
```

### Add Field to Section
- **Endpoint:** `POST /admin/form-fields/sections/{section}/fields`
- **Description:** Add field to section
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/sections/1/fields \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "label": "First Name",
    "type": "text",
    "required": true
  }'
```

### Update Field
- **Endpoint:** `PUT /admin/form-fields/fields/{field}`
- **Description:** Update field
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/form-fields/fields/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"label": "First Name"}'
```

### Delete Field
- **Endpoint:** `DELETE /admin/form-fields/fields/{field}`
- **Description:** Delete field
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/form-fields/fields/1 \
  -H "Authorization: Bearer {token}"
```

### Toggle Field
- **Endpoint:** `POST /admin/form-fields/fields/{field}/toggle`
- **Description:** Toggle field
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/fields/1/toggle \
  -H "Authorization: Bearer {token}"
```

### Toggle Section
- **Endpoint:** `POST /admin/form-fields/sections/{section}/toggle`
- **Description:** Toggle section
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/sections/1/toggle \
  -H "Authorization: Bearer {token}"
```

### Reset to Defaults
- **Endpoint:** `POST /admin/form-fields/reset`
- **Description:** Reset form fields to defaults
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/reset \
  -H "Authorization: Bearer {token}"
```

### Reorder
- **Endpoint:** `POST /admin/form-fields/reorder`
- **Description:** Reorder form fields
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/form-fields/reorder \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"sections": [1, 2, 3]}'
```

---

## 22. Admin FAQs

### List FAQs
- **Endpoint:** `GET /admin/faqs`
- **Description:** List FAQs
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/faqs \
  -H "Authorization: Bearer {token}"
```

### Create FAQ
- **Endpoint:** `POST /admin/faqs`
- **Description:** Create FAQ
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/faqs \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "What is the fee?",
    "answer": "The fee is KES 2000"
  }'
```

### Update FAQ
- **Endpoint:** `PUT /admin/faqs/{faq}`
- **Description:** Update FAQ
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/faqs/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"answer": "Updated answer"}'
```

### Delete FAQ
- **Endpoint:** `DELETE /admin/faqs/{faq}`
- **Description:** Delete FAQ
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/faqs/1 \
  -H "Authorization: Bearer {token}"
```

### Toggle FAQ
- **Endpoint:** `POST /admin/faqs/{faq}/toggle`
- **Description:** Toggle FAQ
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/faqs/1/toggle \
  -H "Authorization: Bearer {token}"
```

### Reorder FAQs
- **Endpoint:** `POST /admin/faqs/reorder`
- **Description:** Reorder FAQs
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/faqs/reorder \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"faqs": [1, 2, 3]}'
```

---

## 23. Admin Settings

### List Settings
- **Endpoint:** `GET /admin/settings`
- **Description:** List settings
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/settings \
  -H "Authorization: Bearer {token}"
```

### Update Settings
- **Endpoint:** `PUT /admin/settings`
- **Description:** Update settings
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/settings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"app_name": "My Portal"}'
```

### Update Settings Group
- **Endpoint:** `POST /admin/settings/{group}`
- **Description:** Update settings group
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/settings/general \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"app_name": "My Portal"}'
```

---

## 24. Admin AI Settings

### AI Settings
- **Endpoint:** `GET /admin/ai-settings`
- **Description:** Get AI settings
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-settings \
  -H "Authorization: Bearer {token}"
```

### Update AI Settings
- **Endpoint:** `POST /admin/ai-settings`
- **Description:** Update AI settings
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-settings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "provider": "openrouter",
    "api_key": "key"
  }'
```

### Add Knowledge
- **Endpoint:** `POST /admin/ai-settings/knowledge`
- **Description:** Add AI knowledge
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-settings/knowledge \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"content": "Knowledge content"}'
```

### Delete Knowledge
- **Endpoint:** `DELETE /admin/ai-settings/knowledge/{knowledge}`
- **Description:** Delete knowledge
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/ai-settings/knowledge/1 \
  -H "Authorization: Bearer {token}"
```

### Start Crawl
- **Endpoint:** `POST /admin/ai-settings/crawl`
- **Description:** Start web crawl
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-settings/crawl \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com"}'
```

### Crawl Status
- **Endpoint:** `GET /admin/ai-settings/crawl/{crawlJob}/status`
- **Description:** Get crawl status
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-settings/crawl/1/status \
  -H "Authorization: Bearer {token}"
```

### Regenerate Key
- **Endpoint:** `POST /admin/ai-settings/regenerate-key`
- **Description:** Regenerate API key
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-settings/regenerate-key \
  -H "Authorization: Bearer {token}"
```

### Generate Embed Code
- **Endpoint:** `POST /admin/ai-settings/embed-code`
- **Description:** Generate embed code
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-settings/embed-code \
  -H "Authorization: Bearer {token}"
```

---

## 25. Admin AI Conversations

### List Conversations
- **Endpoint:** `GET /admin/ai-conversations`
- **Description:** List AI conversations
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-conversations \
  -H "Authorization: Bearer {token}"
```

### Escalated Conversations
- **Endpoint:** `GET /admin/ai-conversations/escalated`
- **Description:** List escalated conversations
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-conversations/escalated \
  -H "Authorization: Bearer {token}"
```

### View Conversation
- **Endpoint:** `GET /admin/ai-conversations/{conversation}`
- **Description:** View conversation
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-conversations/1 \
  -H "Authorization: Bearer {token}"
```

### Update Conversation
- **Endpoint:** `POST /admin/ai-conversations/{conversation}`
- **Description:** Update conversation
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-conversations/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status": "closed"}'
```

### Delete Conversation
- **Endpoint:** `DELETE /admin/ai-conversations/{conversation}`
- **Description:** Delete conversation
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/ai-conversations/1 \
  -H "Authorization: Bearer {token}"
```

### Resolve Conversation
- **Endpoint:** `POST /admin/ai-conversations/{conversation}/resolve`
- **Description:** Resolve conversation
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-conversations/1/resolve \
  -H "Authorization: Bearer {token}"
```

---

## 26. Admin AI Leads

### List Leads
- **Endpoint:** `GET /admin/ai-leads`
- **Description:** List AI leads
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-leads \
  -H "Authorization: Bearer {token}"
```

### Export Leads
- **Endpoint:** `GET /admin/ai-leads-export`
- **Description:** Export AI leads
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-leads-export \
  -H "Authorization: Bearer {token}"
```

### View Lead
- **Endpoint:** `GET /admin/ai-leads/{lead}`
- **Description:** View lead
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/ai-leads/1 \
  -H "Authorization: Bearer {token}"
```

### Update Lead
- **Endpoint:** `POST /admin/ai-leads/{lead}`
- **Description:** Update lead
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/ai-leads/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status": "contacted"}'
```

### Delete Lead
- **Endpoint:** `DELETE /admin/ai-leads/{lead}`
- **Description:** Delete lead
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/ai-leads/1 \
  -H "Authorization: Bearer {token}"
```

---

## 27. Admin Messages

### List Conversations
- **Endpoint:** `GET /admin/messages`
- **Description:** List conversations
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/messages \
  -H "Authorization: Bearer {token}"
```

### Create Conversation
- **Endpoint:** `POST /admin/messages`
- **Description:** Create conversation
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/messages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 2,
    "message": "Hello"
  }'
```

### View Conversation
- **Endpoint:** `GET /admin/messages/{conversation}`
- **Description:** View conversation
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/messages/1 \
  -H "Authorization: Bearer {token}"
```

### Send Message
- **Endpoint:** `POST /admin/messages/{conversation}/messages`
- **Description:** Send message
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/messages/1/messages \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello"}'
```

### Start Conversation
- **Endpoint:** `POST /admin/messages/start`
- **Description:** Start conversation
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/messages/start \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```

### Archive Conversation
- **Endpoint:** `POST /admin/messages/{conversation}/archive`
- **Description:** Archive conversation
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/messages/1/archive \
  -H "Authorization: Bearer {token}"
```

### Mark as Read
- **Endpoint:** `POST /admin/messages/{conversation}/read`
- **Description:** Mark as read
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/messages/1/read \
  -H "Authorization: Bearer {token}"
```

---

## 28. Admin Inquiries

### List Inquiries
- **Endpoint:** `GET /admin/inquiries`
- **Description:** List inquiries
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/inquiries \
  -H "Authorization: Bearer {token}"
```

### Create Inquiry
- **Endpoint:** `POST /admin/inquiries`
- **Description:** Create inquiry
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/inquiries \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John",
    "email": "john@example.com",
    "message": "Hello"
  }'
```

### View Inquiry
- **Endpoint:** `GET /admin/inquiries/{inquiry}`
- **Description:** View inquiry
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/inquiries/1 \
  -H "Authorization: Bearer {token}"
```

### Delete Inquiry
- **Endpoint:** `DELETE /admin/inquiries/{inquiry}`
- **Description:** Delete inquiry
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/inquiries/1 \
  -H "Authorization: Bearer {token}"
```

### Reply Inquiry
- **Endpoint:** `POST /admin/inquiries/{inquiry}/reply`
- **Description:** Reply to inquiry
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/inquiries/1/reply \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reply": "Thank you for your inquiry"}'
```

### Assign Inquiry
- **Endpoint:** `POST /admin/inquiries/{inquiry}/assign`
- **Description:** Assign inquiry
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/inquiries/1/assign \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```

### Update Status
- **Endpoint:** `POST /admin/inquiries/{inquiry}/status`
- **Description:** Update inquiry status
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/inquiries/1/status \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status": "resolved"}'
```

---

## 29. Admin Subscriptions

### View Subscription
- **Endpoint:** `GET /admin/subscription`
- **Description:** View subscription
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/subscription \
  -H "Authorization: Bearer {token}"
```

### Choose Plan
- **Endpoint:** `GET /admin/subscription/choose-plan`
- **Description:** Choose subscription plan
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/subscription/choose-plan \
  -H "Authorization: Bearer {token}"
```

### Subscribe
- **Endpoint:** `POST /admin/subscription/subscribe/{plan}`
- **Description:** Subscribe to plan
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/subscription/subscribe/1 \
  -H "Authorization: Bearer {token}"
```

### Renew
- **Endpoint:** `POST /admin/subscription/renew/{subscription}`
- **Description:** Renew subscription
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/subscription/renew/1 \
  -H "Authorization: Bearer {token}"
```

### Cancel
- **Endpoint:** `POST /admin/subscription/cancel/{subscription}`
- **Description:** Cancel subscription
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/subscription/cancel/1 \
  -H "Authorization: Bearer {token}"
```

---

## 30. Admin Profile

### Edit Profile
- **Endpoint:** `GET /admin/profile`
- **Description:** Edit profile
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/admin/profile \
  -H "Authorization: Bearer {token}"
```

### Update Profile
- **Endpoint:** `PUT /admin/profile`
- **Description:** Update profile
- **Auth:** Required

```bash
curl -X PUT http://localhost:8000/admin/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"first_name": "John"}'
```

### Toggle Dark Mode
- **Endpoint:** `POST /admin/profile/dark-mode`
- **Description:** Toggle dark mode
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/admin/profile/dark-mode \
  -H "Authorization: Bearer {token}"
```

### Update Password
- **Endpoint:** `PUT /admin/profile/password`
- **Description:** Update password
- **Auth:** Required

```bash
curl -X PUT http://localhost:8000/admin/profile/password \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "old",
    "password": "new",
    "password_confirmation": "new"
  }'
```

---

## 31. Admin Notifications

### List Notifications
- **Endpoint:** `GET /admin/notifications`
- **Description:** List notifications
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/notifications \
  -H "Authorization: Bearer {token}"
```

### Send Notification
- **Endpoint:** `POST /admin/notifications/send`
- **Description:** Send notification
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/notifications/send \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "title": "Notification",
    "message": "Message"
  }'
```

### Bulk Send
- **Endpoint:** `POST /admin/notifications/send-bulk`
- **Description:** Bulk send notifications
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/notifications/send-bulk \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_ids": [1, 2, 3],
    "title": "Notification"
  }'
```

### Mark All Read
- **Endpoint:** `POST /admin/notifications/mark-all`
- **Description:** Mark all as read
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/notifications/mark-all \
  -H "Authorization: Bearer {token}"
```

---

## 32. Admin Letter Templates

### List Templates
- **Endpoint:** `GET /admin/letter-templates`
- **Description:** List templates
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/letter-templates \
  -H "Authorization: Bearer {token}"
```

### Create Template
- **Endpoint:** `POST /admin/letter-templates`
- **Description:** Create template
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/letter-templates \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Offer Letter",
    "content": "Template content"
  }'
```

### Update Template
- **Endpoint:** `PUT /admin/letter-templates/{template}`
- **Description:** Update template
- **Auth:** Required (Admin)

```bash
curl -X PUT http://localhost:8000/admin/letter-templates/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"content": "Updated content"}'
```

### Delete Template
- **Endpoint:** `DELETE /admin/letter-templates/{template}`
- **Description:** Delete template
- **Auth:** Required (Admin)

```bash
curl -X DELETE http://localhost:8000/admin/letter-templates/1 \
  -H "Authorization: Bearer {token}"
```

### Activate Template
- **Endpoint:** `POST /admin/letter-templates/{template}/activate`
- **Description:** Activate template
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/letter-templates/1/activate \
  -H "Authorization: Bearer {token}"
```

### Generate Preview
- **Endpoint:** `GET /admin/letter-templates/generate`
- **Description:** Generate preview
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/letter-templates/generate \
  -H "Authorization: Bearer {token}"
```

### History
- **Endpoint:** `GET /admin/letter-templates/{template}/history`
- **Description:** Template version history
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/letter-templates/1/history \
  -H "Authorization: Bearer {token}"
```

### Revert
- **Endpoint:** `POST /admin/letter-templates/{template}/revert/{version}`
- **Description:** Revert to version
- **Auth:** Required (Admin)

```bash
curl -X POST http://localhost:8000/admin/letter-templates/1/revert/1 \
  -H "Authorization: Bearer {token}"
```

---

## 33. Admin Audit Logs

### List Audit Logs
- **Endpoint:** `GET /admin/audit-logs`
- **Description:** List audit logs
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/audit-logs \
  -H "Authorization: Bearer {token}"
```

### Export Audit Logs
- **Endpoint:** `GET /admin/audit-logs/export`
- **Description:** Export audit logs
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/audit-logs/export \
  -H "Authorization: Bearer {token}"
```

### View Audit Log
- **Endpoint:** `GET /admin/audit-logs/{auditLog}`
- **Description:** View audit log
- **Auth:** Required (Admin)

```bash
curl -X GET http://localhost:8000/admin/audit-logs/1 \
  -H "Authorization: Bearer {token}"
```

---

## 34. Super Admin Schools

### List Schools
- **Endpoint:** `GET /super-admin/schools`
- **Description:** List schools
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/schools \
  -H "Authorization: Bearer {token}"
```

### Create School
- **Endpoint:** `POST /super-admin/schools`
- **Description:** Create school
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/schools \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "School Name",
    "slug": "school-name"
  }'
```

### Update School
- **Endpoint:** `PUT /super-admin/schools/{school}`
- **Description:** Update school
- **Auth:** Required (Super Admin)

```bash
curl -X PUT http://localhost:8000/super-admin/schools/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "School Name"}'
```

### Delete School
- **Endpoint:** `DELETE /super-admin/schools/{school}`
- **Description:** Delete school
- **Auth:** Required (Super Admin)

```bash
curl -X DELETE http://localhost:8000/super-admin/schools/1 \
  -H "Authorization: Bearer {token}"
```

### Activate School
- **Endpoint:** `POST /super-admin/schools/{school}/activate`
- **Description:** Activate school
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/schools/1/activate \
  -H "Authorization: Bearer {token}"
```

### Suspend School
- **Endpoint:** `POST /super-admin/schools/{school}/suspend`
- **Description:** Suspend school
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/schools/1/suspend \
  -H "Authorization: Bearer {token}"
```

---

## 35. Super Admin Programs

### List Global Programs
- **Endpoint:** `GET /super-admin/programs`
- **Description:** List global programs
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/programs \
  -H "Authorization: Bearer {token}"
```

### Create Global Program
- **Endpoint:** `POST /super-admin/programs`
- **Description:** Create global program
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/programs \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Computer Science",
    "code": "CS"
  }'
```

---

## 36. Super Admin Backups

### List Backups
- **Endpoint:** `GET /super-admin/backups`
- **Description:** List backups
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/backups \
  -H "Authorization: Bearer {token}"
```

### Create Backup
- **Endpoint:** `POST /super-admin/backups`
- **Description:** Create backup
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/backups \
  -H "Authorization: Bearer {token}"
```

### Restore Backup
- **Endpoint:** `POST /super-admin/backups/{backup}/restore`
- **Description:** Restore backup
- **Auth:** Required (Super Admin)

```bash
curl -X POST http://localhost:8000/super-admin/backups/1/restore \
  -H "Authorization: Bearer {token}"
```

### Download Backup
- **Endpoint:** `GET /super-admin/backups/{backup}/download`
- **Description:** Download backup
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/backups/1/download \
  -H "Authorization: Bearer {token}"
```

---

## 37. Super Admin Activity Logs

### List Activity Logs
- **Endpoint:** `GET /super-admin/activity-logs`
- **Description:** List activity logs
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/activity-logs \
  -H "Authorization: Bearer {token}"
```

### Export Activity Logs
- **Endpoint:** `GET /super-admin/activity-logs/export`
- **Description:** Export activity logs
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/activity-logs/export \
  -H "Authorization: Bearer {token}"
```

### Statistics
- **Endpoint:** `GET /super-admin/activity-logs/statistics`
- **Description:** Get statistics
- **Auth:** Required (Super Admin)

```bash
curl -X GET http://localhost:8000/super-admin/activity-logs/statistics \
  -H "Authorization: Bearer {token}"
```

---

## 38. Super Admin Reports

### Applications Report
- **Endpoint:** `GET /super-admin/reports/applications`
- **Description:** Applications report
- **Auth:** Required (Super Admin)

### Schools Report
- **Endpoint:** `GET /super-admin/reports/schools`
- **Description:** Schools report
- **Auth:** Required (Super Admin)

### Financial Report
- **Endpoint:** `GET /super-admin/reports/financial`
- **Description:** Financial report
- **Auth:** Required (Super Admin)

---

## 39. AI Endpoints

### AI Chat
- **Endpoint:** `POST /ai/chat`
- **Description:** AI chat
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/ai/chat \
  -H "Content-Type: application/json" \
  -d '{
    "message": "What programs do you offer?",
    "school_id": 1
  }'
```

**Response:**
```json
{
  "response": "We offer Computer Science, Business, etc.",
  "conversation_id": 1
}
```

### Admission Flow
- **Endpoint:** `POST /ai/admission-flow`
- **Description:** AI admission flow
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/ai/admission-flow \
  -H "Content-Type: application/json" \
  -d '{
    "step": "program",
    "data": {}
  }'
```

### Embed Chat
- **Endpoint:** `POST /ai/embed-chat`
- **Description:** Embedded AI chat
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/ai/embed-chat \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Hello",
    "school_id": 1
  }'
```

### Chat History
- **Endpoint:** `GET /ai/history`
- **Description:** Get chat history
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/ai/history
```

### Clear History
- **Endpoint:** `POST /ai/clear`
- **Description:** Clear chat history
- **Auth:** Not Required

```bash
curl -X POST http://localhost:8000/ai/clear
```

### Greeting
- **Endpoint:** `GET /ai/greeting`
- **Description:** Get greeting message
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/ai/greeting
```

---

## 40. Sessions

### Active Sessions
- **Endpoint:** `GET /sessions`
- **Description:** List active sessions
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/sessions \
  -H "Authorization: Bearer {token}"
```

### Logout All Devices
- **Endpoint:** `POST /sessions/logout-all`
- **Description:** Logout all devices
- **Auth:** Required

```bash
curl -X POST http://localhost:8000/sessions/logout-all \
  -H "Authorization: Bearer {token}"
```

### Terminate Session
- **Endpoint:** `GET /sessions/{sessionId}/terminate`
- **Description:** Terminate specific session
- **Auth:** Required

```bash
curl -X GET http://localhost:8000/sessions/1/terminate \
  -H "Authorization: Bearer {token}"
```

---

## 41. Language

### Switch Language
- **Endpoint:** `GET /lang/{locale}`
- **Description:** Switch language
- **Auth:** Not Required

```bash
curl -X GET http://localhost:8000/lang/en
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid input data |
| 401 | Unauthorized - Invalid or missing token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Validation Error - Input validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Server Error - Internal server error |

---

## Rate Limiting

- **API Routes**: 60 requests per minute
- **Authentication**: 5 requests per minute

---

## Webhooks

| Endpoint | Description |
|----------|-------------|
| `/mpesa/callback` | M-Pesa payment callback |
| `/mpesa/confirmation` | M-Pesa confirmation |
| `/mpesa/validation` | M-Pesa validation |
| `/mpesa/result` | M-Pesa result |

---

*Generated: July 2026*  
*Project Start Date: March 13, 2027*
*Total Endpoints: 215+*