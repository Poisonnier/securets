# SecureTS Integration — Product Requirements Document


## 1. Product Overview 

SecureTS (Threat Shield) is an authentication integration that allows an external application, referred to as the **Target App**, to use SecureTS to protect user account access.

When a user attempts to access a protected Target App account, the Target App sends an access request to SecureTS. SecureTS then sends a real-time notification to the user's SecureTS mobile application, where the user can approve or deny the request.
The integration focuses on the **account and authentication-related parts** of the Target App and does not interfere with its other functions.

## 2. Problem Statement

User accounts can be exposed when unauthorized individuals obtain access to account credentials.

SecureTS aims to reduce this risk by requiring a separate approval from the user's registered SecureTS device before access to a protected Target App account is granted.

## 3. Goals

- Protect Target App accounts from unauthorized access
- Provide real-time login/access approval through the SecureTS mobile application
- Allow a Target App to connect its user accounts with SecureTS
- Allow users to approve or deny access requests
- Keep the integration focused on authentication rather than the Target App's other features

## 4. Non-Goals


SecureTS integration does not:

- Manage the Target App's business functions.
- Modify the Target App's unrelated features.
- Manage the Target App's products, services, or content.
- Replace the Target App's internal application architecture.
- Control the Target App's internal database structure.

## 5. Target Users

#### End User

Users who have an account in a Target App and use SecureTS to verify access to that account.

#### Target App Development Teams

Developers responsible for integrating their application with SecureTS and connecting their application's account authentication flow to SecureTS.

## 6. User Roles

#### Target App User

The person attempting to access a protected account.

#### Target App

The external system that integrates with SecureTS and requests authentication.

#### SecureTS User

The user of the SecureTS mobile application who receives and responds to authentication requests.

#### SecureTS Backend

The service responsible for processing integration, registration, authentication requests, and authentication results.

## 7. User Stories

#### Target App User

- As a Target App user, I want to connect my account to SecureTS so that my account can be protected.
- As a Target App user, I want to receive an authentication request on my SecureTS mobile app when I attempt to access my account.
- As a user, I want to approve an access request so that I can continue accessing my account.
- As a user, I want to deny an access request so that an unauthorized person cannot access my account.

#### Target App Developer

- As a developer, I want to integrate my application with SecureTS so that user accounts can use SecureTS authentication.
- As a developer, I want to receive the authentication result from SecureTS so that my application can allow or deny access.

## 8. Functional Requirements

#### FR-01 — Target App Integration

The system shall allow a Target App to integrate with SecureTS.

#### FR-02 — Integration Credentials

SecureTS shall provide a unique Integration ID and API Secret for the Target App integration.

#### FR-03 — User Setup

The integration shall allow a user to connect their Target App account with SecureTS.

#### FR-04 — Setup Token

The Target App shall be able to request a setup payload from SecureTS, and SecureTS shall generate a setup token.

#### FR-05 — Token Setup Integration

The Target App shall present the setup token to the user, such as through a direct link.

#### FR-06 — Token Validation

SecureTS shall validate the setup token submitted through the SecureTS mobile application.

#### FR-07 — Instance Token

SecureTS shall generate a unique Instance Token for the user's registered device.

#### FR-08 — Integration Confirmation

SecureTS shall notify the Target App when the user integration is successfully completed.

#### FR-09 — Access Request

The Target App shall send an access request to SecureTS when a protected user attempts to access the application.

#### FR-10 — User Identification

The access request shall include the relevant Instance Token ID so SecureTS can identify the associated registration.

#### FR-11 — Authentication Notification

SecureTS shall send a real-time authentication notification to the user's SecureTS mobile application.

#### FR-12 — Approval

The user shall be able to approve an authentication request.

#### FR-13 — Denial

The user shall be able to deny an authentication request.

#### FR-14 — Authentication Result

SecureTS shall provide the resulting authentication decision to the Target App.

#### FR-15 — Access Control

The Target App shall allow or deny access based on the authentication result.

## 9. Non-Functional Requirements

#### NFR-01 — Security

Authentication-related communication and credentials shall be protected from unauthorized access.

#### NFR-02 — Responsiveness

Authentication requests should be delivered to the SecureTS mobile application in real time or near real time, consistent with the intended push-notification model.

#### NFR-03 — Separation

The integration should interact only with the authentication-related portions of the Target App.

#### NFR-04 — Reliability

The integration should handle authentication requests consistently and return a clear authentication result to the Target App.

## 10. Business Rules

#### BR-01

A Target App must be registered with SecureTS before it can use the integration.

#### BR-02

A user must complete the SecureTS setup process before SecureTS can be used to authenticate that user's protected account.

#### BR-03

An authentication request must be associated with the relevant registered user/device.

#### BR-04

An access request must be approved before protected access is granted.

#### BR-05

If the user denies the request, access shall not be granted.

#### BR-06

The Target App remains responsible for its own application functionality outside the authentication integration.

## 11. User Flows

### 11.1 Target App Integration Flow

Target App -> Register with SecureTS -> Receive Integration ID + API Secret -> Integration Ready

The current design specifies that SecureTS generates the Integration ID and API secret as part of the integration setup.

### 11.2 User Setup Flow

User -> Logs into SecureTS -> Selects Target App -> Target App requests setup payload -> SecureTS generates setup token -> Target App displays link -> User scans or enters token -> SecureTS validates token -> SecureTS generates Instance Token -> Integration completed

This follows the setup process described in the source document.

### 11.3 Authentication Flow

User attempts access -> Target App identifies user -> Target App sends Access Request -> SecureTS identifies registered device -> SecureTS sends push notification -> User selects Approve / Deny -> SecureTS returns authentication result -> Target App allows / denies access

The source document explicitly describes the Target App access request and SecureTS push-notification stage.

## 12. Acceptance Criteria

#### AC-01 — Integration

A Target App can be registered with SecureTS and receive the required integration credentials.

#### AC-02 — User Registration

A user can connect their Target App account with SecureTS using the setup process.

#### AC-03 — Device Registration

A valid setup process results in an Instance Token associated with the user's registered device.

#### AC-04 — Authentication Request

When a protected user attempts to access the Target App, an authentication request can be sent to SecureTS.

#### AC-05 — Notification

The user receives the authentication request through the SecureTS mobile application.

#### AC-06 — Approval

When the user approves the request, the Target App receives a successful authentication result and may allow access.

#### AC-07 — Denial

When the user denies the request, the Target App receives an unsuccessful authentication result and shall deny access.

## 13. Security Requirements

- Integration credentials must be protected from unauthorized use.
- Setup tokens must not allow unauthorized users to register an account or device.
- Instance Tokens must be protected because they are associated with the user's registered device.
- Authentication requests must be associated with the correct Target App user/device.
- Unauthorized or invalid authentication requests must not result in account access.
- Approval and denial actions must affect only the corresponding authentication request.

The current document establishes the use of a device-bound Instance Token, but does not specify the exact implementation of token expiration, revocation, or replay protection.

## 14. Technical Constraints

The integration must support:

- A Target App backend communicating with SecureTS.
- A SecureTS backend responsible for authentication processing.
- A SecureTS mobile application capable of receiving real-time authentication requests.
- A mechanism for the Target App to present a setup token to the user, such as a QR code or direct link.
- Secure communication between the involved systems.

## 15. Scope & Priorities

#### Priority 1 - Core Authentication

- Target App integration.
- User/account linking.
- Device registration.
- Authentication request.
- SecureTS mobile approval/denial.
- Authentication result.
- Target App access decision.

#### Priority 2 - Integration Support

- Setup token process.
- Integration credentials.
- Instance Token handling.
- Integration confirmation.

#### Priority 3 - Additional Security and Reliability

- Token expiration.
- Token revocation.
- Authentication request expiration.
- Device management.
- Failure handling.
- Audit logging.

## 16. Open Questions

1. Does SecureTS replace the Target App's password authentication, or does it act as an additional authentication step?
2. How exactly is the Target App account associated with the SecureTS user?
3. Is the Instance Token a secret credential, an identifier, or both?
4. How long do setup tokens remain valid?
5. Are setup tokens single-use?
6. Can a user register multiple devices?
7. How can a lost or replaced device be removed or revoked?
8. How does SecureTS return the approval/denial result to the Target App?
9. What happens when the user does not respond to an authentication request?
10. What happens when the SecureTS mobile device is offline?
11. How are invalid, expired, or repeated authentication requests handled?
12. What authentication mechanism will be used for communication between SecureTS and the Target App?
13. What information should be displayed to the user with an authentication request?

These questions will be resolved before the integration is finalized for implementation.
