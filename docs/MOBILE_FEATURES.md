# Mobile Application Features & Implementation Plan

## Project Overview

This document outlines the complete feature set and phased implementation plan for the KMTC Admission Portal mobile application (Android).

**Project Start Date:** March 13, 2027

---

## Feature Categories

| Category | Description |
|----------|-------------|
| Authentication | User login, registration, password reset, 2FA |
| Student Portal | Dashboard, profile management, application tracking |
| Admissions | Program browsing, application form, document upload |
| Payments | Fee payment via M-PESA, PayPal, bank transfer |
| Notifications | Push notifications, in-app alerts |
| Communications | Chat with support, AI assistant (Eliana D) |
| Settings | App preferences, language, security |

---

## Implementation Phases

### Phase 1: Core Authentication & Registration

**Duration:** 1 week  
**Priority:** Critical

#### Features
- [ ] Email/password login
- [ ] User registration with school selection
- [ ] Password reset via email
- [ ] Session management (logout, token refresh)
- [ ] Biometric login (fingerprint/face)
- [ ] Splash screen with school branding

#### API Endpoints Required
```
POST /api/v1/auth/login
POST /api/v1/auth/register
POST /api/v1/auth/logout
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
GET  /api/v1/auth/me
```

#### Technical Requirements
- JWT/Sanctum token management
- Secure token storage (EncryptedSharedPreferences)
- Auto-refresh token logic
- Biometric authentication integration

---

### Phase 2: Student Dashboard & Profile

**Duration:** 1 week  
**Priority:** High

#### Features
- [ ] Student dashboard with application status
- [ ] View personal information
- [ ] Edit profile (name, phone, photo)
- [ ] Change password
- [ ] View application history
- [ ] Quick stats (applications, status)

#### API Endpoints Required
```
GET  /api/v1/applications
GET  /api/v1/applications/{id}
PUT  /api/v1/auth/profile
PUT  /api/v1/auth/password
GET  /api/v1/school/settings
```

#### UI Components
- Dashboard cards showing application status
- Profile screen with editable fields
- Application list with status badges

---

### Phase 3: Program & Intake Browsing

**Duration:** 1 week  
**Priority:** High

#### Features
- [ ] Browse all programs by school
- [ ] Filter programs by department
- [ ] Search programs by name/code
- [ ] View program details (requirements, duration, fees)
- [ ] View current and upcoming intakes
- [ ] Program eligibility checker

#### API Endpoints Required
```
GET  /api/v1/schools/{slug}
GET  /api/v1/schools/{slug}/programs
GET  /api/v1/programs
GET  /api/v1/programs/{id}
GET  /api/v1/programs/{id}/requirements
POST /api/v1/programs/check-eligibility
GET  /api/v1/intakes
GET  /api/v1/intakes/current
GET  /api/v1/departments
```

#### UI Components
- Program list with cards
- Filter chips (department, level)
- Search bar with suggestions
- Program detail screen with tabs

---

### Phase 4: Application Form

**Duration:** 2 weeks  
**Priority:** High

#### Features
- [ ] Multi-step application form
  - Step 1: Personal Information
  - Step 2: Academic Background
  - Step 3: Guardian/Parent Info
  - Step 4: Document Upload
  - Step 5: Review & Submit
- [ ] Auto-save draft functionality
- [ ] Resume incomplete application
- [ ] Document upload (photo, certificates)
- [ ] Application preview as PDF
- [ ] Submit application

#### API Endpoints Required
```
POST /api/v1/applications
GET  /api/v1/applications/create
PUT  /api/v1/applications/{id}
POST /api/v1/applications/{id}/submit
GET  /api/v1/applications/current-intake
```

#### Technical Requirements
- File picker for documents
- Image compression before upload
- Offline cache for draft saving
- Progress indicator per step

---

### Phase 5: Payment Integration

**Duration:** 1.5 weeks  
**Priority:** High

#### Features
- [ ] View payment history
- [ ] Initiate M-PESA payment (STK Push)
- [ ] PayPal integration
- [ ] Bank transfer instructions
- [ ] View payment receipt
- [ ] Payment status tracking
- [ ] Payment confirmation screen

#### API Endpoints Required
```
GET  /api/v1/payments
GET  /api/v1/payments/{id}
POST /api/v1/payments/initiate
GET  /api/v1/payments/{id}/status
POST /api/v1/payments/callback
```

#### Technical Requirements
- M-PESA SDK integration
- PayPal SDK integration
- Background payment status polling
- Push notification for payment success

---

### Phase 6: Notifications

**Duration:** 1 week  
**Priority:** Medium

#### Features
- [ ] In-app notification list
- [ ] Push notifications (Firebase)
- [ ] Mark notifications as read
- [ ] Notification preferences
- [ ] Notification categories

#### API Endpoints Required
```
GET  /api/v1/notifications
GET  /api/v1/notifications/unread-count
POST /api/v1/notifications/{id}/read
POST /api/v1/notifications/mark-all-read
DELETE /api/v1/notifications/{id}
```

#### Technical Requirements
- Firebase Cloud Messaging (FCM)
- Local notification scheduling
- Deep linking to relevant screens

---

### Phase 7: Support & Communications

**Duration:** 1.5 weeks  
**Priority:** Medium

#### Features
- [ ] In-app messaging with support staff
- [ ] Create new conversation
- [ ] View conversation history
- [ ] Send/receive messages
- [ ] Upload attachments in chat
- [ ] AI Assistant (Eliana D) integration

#### API Endpoints Required
```
GET  /api/v1/messages/conversations
POST /api/v1/messages/conversations
GET  /api/v1/messages/conversations/{id}
POST /api/v1/messages/conversations/{id}/messages
GET  /api/v1/ai/chat
POST /api/v1/ai/chat
```

#### Technical Requirements
- WebSocket for real-time messaging
- Message offline queue
- Read receipts
- Typing indicators

---

### Phase 8: Settings & Preferences

**Duration:** 1 week  
**Priority:** Medium

#### Features
- [ ] App language selection (English, Swahili)
- [ ] Dark/Light mode toggle
- [ ] Notification preferences
- [ ] Privacy settings
- [ ] About & App info
- [ ] Clear cache
- [ ] Terms & Conditions
- [ ] Privacy Policy
- [ ] Contact support

#### Technical Requirements
- Room database for preferences
- Data persistence
- Cache management

---

### Phase 9: Advanced Features

**Duration:** 2 weeks  
**Priority:** Low

#### Features
- [ ] Multi-school support (switch schools)
- [ ] Offline mode (view cached data)
- [ ] QR code scanner for events
- [ ] Application status widget
- [ ] Share admission letter
- [ ] Admission status check without login

#### API Endpoints Required
```
GET  /api/v1/auth/me (with school switch)
POST /api/v1/applications/status-check
```

---

## API Endpoints Summary

### Public Endpoints (No Auth Required)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /auth/login | User login |
| POST | /auth/register | User registration |
| POST | /auth/forgot-password | Request password reset |
| POST | /auth/reset-password | Reset password |
| GET | /schools | List schools |
| GET | /schools/{slug} | School details |
| GET | /schools/{slug}/programs | School programs |
| GET | /schools/{slug}/intakes | School intakes |
| GET | /programs | All programs |
| GET | /programs/{id} | Program details |
| GET | /programs/{id}/requirements | Requirements |
| GET | /intakes | All intakes |
| GET | /intakes/current | Current intake |

### Protected Endpoints (Auth Required)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /auth/me | Current user |
| PUT | /auth/profile | Update profile |
| PUT | /auth/password | Change password |
| POST | /auth/verify-2fa | 2FA verification |
| POST | /auth/logout | Logout |
| GET | /applications | User applications |
| POST | /applications | Create application |
| GET | /applications/{id} | Application details |
| PUT | /applications/{id} | Update draft |
| POST | /applications/{id}/submit | Submit application |
| GET | /payments | Payment history |
| POST | /payments/initiate | Initiate payment |
| GET | /payments/{id}/status | Payment status |
| GET | /notifications | User notifications |
| POST | /notifications/{id}/read | Mark as read |
| POST | /notifications/mark-all-read | Mark all read |
| GET | /school/settings | School settings |

---

## Technology Stack Recommendation

### Frontend (Android)
- **Framework:** Kotlin with Jetpack Compose
- **Architecture:** MVVM + Clean Architecture
- **DI:** Hilt / Koin
- **Networking:** Retrofit + OkHttp
- **Local Storage:** Room + DataStore
- **Image Loading:** Coil
- **Push Notifications:** Firebase Cloud Messaging

### Backend (Existing)
- Laravel Sanctum for authentication
- REST API (already implemented)

---

## Implementation Timeline

| Phase | Duration | Features |
|-------|----------|----------|
| Phase 1 | 1 week | Auth & Registration |
| Phase 2 | 1 week | Dashboard & Profile |
| Phase 3 | 1 week | Programs & Intakes |
| Phase 4 | 2 weeks | Application Form |
| Phase 5 | 1.5 weeks | Payment Integration |
| Phase 6 | 1 week | Notifications |
| Phase 7 | 1.5 weeks | Support & Chat |
| Phase 8 | 1 week | Settings |
| Phase 9 | 2 weeks | Advanced Features |

**Total Estimated Time:** 11.5 weeks

---

## Priority Matrix

```
                    High Impact
                        │
    ┌───────────────────┼───────────────────┐
    │                   │                   │
    │   Phase 1         │   Phase 2         │
    │   Phase 3         │   Phase 4         │
    │   Phase 5         │   Phase 6         │
    │                   │                   │
Low │──────────────────┼───────────────────│High
Impact                │                   Impact
    │                   │                   │
    │   Phase 9         │   Phase 7         │
    │   Phase 8         │   Phase 5         │
    │                   │                   │
    └───────────────────┼───────────────────┘
                        │
                    Low Effort
```

---

## Testing Requirements

- Unit tests for ViewModels
- Integration tests for API calls
- UI tests with Espresso
- Payment flow testing (sandbox)
- Security testing (token handling)

---

## Deployment

1. **Internal Testing** - Firebase App Distribution
2. **Beta Testing** - Google Play internal testing
3. **Production** - Google Play Store

---

## Maintenance Plan

- Monthly feature updates
- Security patches
- API version management
- Analytics integration
- User feedback collection