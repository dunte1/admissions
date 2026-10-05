# Admission Portal API Documentation

## Overview
- **Version**: 1.0.0
- **Base URL**: `http://localhost:8000`
- **Authentication**: Laravel Sanctum (Bearer Token)
- **Content-Type**: application/json

---

## Public Endpoints

### 1. Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/login` | Show login form |
| POST | `/login` | Authenticate user |
| POST | `/logout` | Logout user |
| GET | `/register` | Show registration form |
| POST | `/register` | Register new student |
| GET | `/forgot-password` | Show forgot password form |
| POST | `/forgot-password` | Send password reset link |
| GET | `/reset-password/{token}` | Show reset password form |
| POST | `/reset-password` | Reset password |
| GET | `/email/verify/{id}/{hash}` | Verify email |

### 2. Public API

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/program/eligibility` | Check program eligibility | None |
| GET | `/api/program/{programId}/requirements` | Get program requirements | None |
| GET | `/api/user` | Get authenticated user | Required |

### 3. Landing Page

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Landing page |
| POST | `/contact` | Submit contact form |

### 4. Student Application

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/dashboard` | Student dashboard |
| GET | `/student/application/create` | Create application |
| POST | `/student/application` | Store application |
| GET | `/student/application/{application}` | View application |
| POST | `/student/application/save/{step}` | Save form step |
| POST | `/student/application/submit/{application}` | Submit application |
| GET | `/student/application/review/{application}` | Review application |
| GET | `/student/application/form/{step}` | Show application form step |
| GET | `/student/application/{application}/pdf` | Download application PDF |

### 5. Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/payment/{application}` | Show payment page |
| POST | `/student/payment/{application}/initiate` | Initiate payment |
| POST | `/student/payment/initiate-mpesa` | Initiate M-Pesa |
| POST | `/student/payment/{application}/manual` | Submit manual payment |
| GET | `/student/payment/{application}/status` | Check payment status |
| GET | `/student/payment/{payment}/receipt` | Download receipt |
| POST | `/student/payment/{application}/callback` | Payment callback |

### 6. M-Pesa Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/mpesa/callback` | M-Pesa callback |
| POST | `/mpesa/confirmation` | Payment confirmation |
| POST | `/mpesa/validation` | Payment validation |
| GET | `/mpesa/result` | Payment result |

### 7. Offers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/offers` | List offers |
| GET | `/student/offers/{offer}` | View offer |
| POST | `/student/offers/{offer}/accept` | Accept offer |
| POST | `/student/offers/{offer}/decline` | Decline offer |
| GET | `/student/offers/{offer}/download` | Download offer letter |

### 8. Student Profile

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/profile` | Edit profile |
| PUT | `/student/profile` | Update profile |
| POST | `/student/profile/dark-mode` | Toggle dark mode |
| PUT | `/student/profile/password` | Update password |

### 9. Student Messages

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/messages` | List conversations |
| POST | `/student/messages` | Create conversation |
| GET | `/student/messages/{conversation}` | View conversation |
| POST | `/student/messages/{conversation}/messages` | Send message |
| POST | `/student/messages/start` | Start new conversation |

---

## Admin Endpoints (Prefix: `/admin`)

### 10. Admin Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/dashboard` | Admin dashboard |
| GET | `/admin/analytics` | Analytics data |
| GET | `/admin/accountant` | Accountant dashboard |

### 11. Applications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/applications` | List applications |
| GET | `/admin/applications/{application}` | View application |
| POST | `/admin/applications/{application}/approve` | Approve application |
| POST | `/admin/applications/{application}/reject` | Reject application |
| POST | `/admin/applications/{application}/request-info` | Request more info |
| GET | `/admin/applications/{application}/pdf` | Export PDF |
| POST | `/admin/applications/bulk-action` | Bulk action |

### 12. Admission Letters

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/admission-letters` | List letters |
| POST | `/admin/admission-letters` | Create letter |
| GET | `/admin/admission-letters/bulk` | Bulk create |
| POST | `/admin/admission-letters/bulk` | Bulk store |
| GET | `/admin/admission-letters/{letter}/download` | Download letter |
| POST | `/admin/admission-letters/{letter}/resend` | Resend letter |

### 13. Programs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/programs` | List programs |
| POST | `/admin/programs` | Create program |
| PUT | `/admin/programs/{program}` | Update program |
| DELETE | `/admin/programs/{program}` | Delete program |
| POST | `/admin/programs/{program}/toggle-status` | Toggle status |
| POST | `/admin/programs/add-global` | Add from global |

### 14. Intakes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/intakes` | List intakes |
| POST | `/admin/intakes` | Create intake |
| PUT | `/admin/intakes/{intake}` | Update intake |
| DELETE | `/admin/intakes/{intake}` | Delete intake |
| POST | `/admin/intakes/{intake}/set-current` | Set as current |

### 15. Users

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/users` | List users |
| PUT | `/admin/users/{user}` | Update user |
| POST | `/admin/users/{user}/reset-password` | Reset password |

### 16. Roles & Permissions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/roles` | List roles |
| POST | `/admin/roles` | Create role |
| PUT | `/admin/roles/{role}` | Update role |
| DELETE | `/admin/roles/{role}` | Delete role |
| POST | `/admin/roles/assign/{user}` | Assign role |
| DELETE | `/admin/roles/assign/{user}` | Revoke role |
| POST | `/admin/roles/assign-bulk` | Bulk assign |

### 17. Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/payments/{payment}` | View payment |
| POST | `/admin/payments/{payment}/verify` | Verify payment |
| POST | `/admin/payments/{payment}/reject` | Reject payment |
| GET | `/admin/payments/{payment}/receipt` | Download receipt |

### 18. Documents

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/documents` | List documents |
| POST | `/admin/documents/{document}/verify` | Verify document |
| POST | `/admin/documents/{document}/reject` | Reject document |
| POST | `/admin/documents/bulk-verify` | Bulk verify |

### 19. Broadcasts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/broadcast` | List broadcasts |
| POST | `/admin/broadcast` | Create broadcast |
| GET | `/admin/broadcast/templates` | List templates |
| POST | `/admin/broadcast/templates` | Store template |
| POST | `/admin/broadcast/{broadcast}/cancel` | Cancel broadcast |
| POST | `/admin/broadcast/{broadcast}/resend` | Resend broadcast |

### 20. Form Fields

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/form-fields` | List form fields |
| POST | `/admin/form-fields/sections` | Create section |
| POST | `/admin/form-fields/sections/{section}/fields` | Add field |
| POST | `/admin/form-fields/reset` | Reset to defaults |

### 21. FAQs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/faqs` | List FAQs |
| POST | `/admin/faqs` | Create FAQ |
| PUT | `/admin/faqs/{faq}` | Update FAQ |
| DELETE | `/admin/faqs/{faq}` | Delete FAQ |
| POST | `/admin/faqs/reorder` | Reorder FAQs |

### 22. Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/settings` | List settings |
| PUT | `/admin/settings` | Update settings |
| POST | `/admin/settings/{group}` | Update group |

### 23. AI Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/ai-settings` | AI settings |
| POST | `/admin/ai-settings` | Update settings |
| POST | `/admin/ai-settings/knowledge` | Add knowledge |
| POST | `/admin/ai-settings/crawl` | Start web crawl |
| POST | `/admin/ai-settings/regenerate-key` | Regenerate API key |
| POST | `/admin/ai-settings/embed-code` | Generate embed code |

### 24. AI Conversations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/ai-conversations` | List conversations |
| GET | `/admin/ai-conversations/escalated` | Escalated conversations |
| GET | `/admin/ai-conversations/{conversation}` | View conversation |
| POST | `/admin/ai-conversations/{conversation}/resolve` | Resolve |

### 25. AI Leads

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/ai-leads` | List leads |
| GET | `/admin/ai-leads-export` | Export leads |
| POST | `/admin/ai-leads/{lead}` | Update lead |
| DELETE | `/admin/ai-leads/{lead}` | Delete lead |

### 26. Messages

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/messages` | List conversations |
| POST | `/admin/messages` | Create conversation |
| GET | `/admin/messages/{conversation}` | View conversation |
| POST | `/admin/messages/{conversation}/messages` | Send message |
| POST | `/admin/messages/start` | Start conversation |

### 27. Inquiries

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/inquiries` | List inquiries |
| POST | `/admin/inquiries` | Create inquiry |
| POST | `/admin/inquiries/{inquiry}/reply` | Reply to inquiry |
| POST | `/admin/inquiries/{inquiry}/assign` | Assign inquiry |
| POST | `/admin/inquiries/{inquiry}/status` | Update status |

### 28. Subscriptions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/subscription` | View subscription |
| GET | `/admin/subscription/choose-plan` | Choose plan |
| POST | `/admin/subscription/subscribe/{plan}` | Subscribe |
| POST | `/admin/subscription/renew/{subscription}` | Renew |
| POST | `/admin/subscription/cancel/{subscription}` | Cancel |

### 29. Profile

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/profile` | Edit profile |
| PUT | `/admin/profile` | Update profile |
| POST | `/admin/profile/dark-mode` | Toggle dark mode |
| PUT | `/admin/profile/password` | Update password |

### 30. Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/notifications` | List notifications |
| POST | `/admin/notifications/send` | Send notification |
| POST | `/admin/notifications/send-bulk` | Bulk send |
| POST | `/admin/notifications/mark-all` | Mark all read |

### 31. Letter Templates

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/letter-templates` | List templates |
| POST | `/admin/letter-templates` | Create template |
| GET | `/admin/letter-templates/generate` | Generate preview |
| PUT | `/admin/letter-templates/{template}` | Update |
| POST | `/admin/letter-templates/{template}/activate` | Activate |
| GET | `/admin/letter-templates/{template}/history` | Version history |
| POST | `/admin/letter-templates/{template}/revert/{version}` | Revert |

### 32. Audit Logs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/audit-logs` | List logs |
| GET | `/admin/audit-logs/export` | Export logs |

---

## Super Admin Endpoints (Prefix: `/super-admin`)

### 33. Schools Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/super-admin/schools` | List schools |
| POST | `/super-admin/schools` | Create school |
| PUT | `/super-admin/schools/{school}` | Update school |
| DELETE | `/super-admin/schools/{school}` | Delete school |
| POST | `/super-admin/schools/{school}/activate` | Activate |
| POST | `/super-admin/schools/{school}/suspend` | Suspend |

### 34. Global Programs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/super-admin/programs` | List global programs |
| POST | `/super-admin/programs` | Create global program |

### 35. Backups

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/super-admin/backups` | List backups |
| POST | `/super-admin/backups` | Create backup |
| POST | `/super-admin/backups/{backup}/restore` | Restore backup |
| GET | `/super-admin/backups/{backup}/download` | Download backup |

### 36. Activity Logs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/super-admin/activity-logs` | List activity logs |
| GET | `/super-admin/activity-logs/statistics` | Statistics |

---

## AI Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/ai/chat` | AI Chat |
| POST | `/ai/admission-flow` | Admission flow |
| POST | `/ai/embed-chat` | Embedded chat |
| GET | `/ai/history` | Chat history |
| POST | `/ai/clear` | Clear history |
| GET | `/ai/greeting` | Greeting message |

---

## Request/Response Examples

### Eligibility Check
```bash
curl -X POST http://localhost:8000/api/program/eligibility \
  -H "Content-Type: application/json" \
  -d '{"program_id": 1, "grade": "A", "year": 2024}'
```

### Response
```json
{
  "eligible": true,
  "message": "You meet the eligibility criteria",
  "requirements_met": ["grade", "year"]
}
```

### Login
```bash
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{"email": "student@example.com", "password": "password"}'
```

### Create Application
```bash
curl -X POST http://localhost:8000/student/application \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"program_id": 1, "first_name": "John", "last_name": "Doe"}'
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## Rate Limiting
- **API**: 60 requests/minute
- **Authentication**: 5 requests/minute

---

## Webhook Endpoints
| Endpoint | Purpose |
|----------|---------|
| `/mpesa/callback` | M-Pesa payments |
| `/webhook/payment` | Generic payments |

---

*Generated on: July 2026*
*Project Start Date: March 13, 2027*
*Total Endpoints: 215+*