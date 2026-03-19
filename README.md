# KnowVerse: Academic Discussion Platform

KnowVerse is a secure, web-based academic discussion platform designed to provide a structured and credible environment for scholarly dialogue. Built to address the shortcomings of general-purpose knowledge-sharing networks, KnowVerse integrates a strict reputation-based governance model, real-time interactivity, and advanced content moderation to ensure academic integrity and meaningful collaboration.

This project was developed as a Bachelor's Graduation Project for the Web Technology & Information Security (WIS) program.

---

## Project Objectives

- Provide a secure environment for students, researchers, and academics to publish and debate scholarly content.
- Ensure academic accountability through an immutable reputation ledger.
- Foster real-time collaboration and knowledge discovery using modern web technologies.
- Maintain high content quality via multi-layered Role-Based Access Control (RBAC) and automated filtering.

---

## System Architecture & Technology Stack

KnowVerse follows a layered monolithic architecture based on the Model-View-Controller (MVC) pattern, ensuring clean separation of concerns, scalability, and maintainability.

**Backend & Core Services**
- Framework: Laravel 12.x (PHP 8.2)
- Real-Time Engine: Laravel Reverb (WebSockets)
- Security: Laravel Breeze, BCrypt Hashing, CSRF/XSS Protection

**Frontend & Presentation**
- Templating: Laravel Blade
- Styling: Tailwind CSS 4.x
- Interactivity: Alpine.js 3.x

**Data & Persistence**
- Database: MySQL 8.x
- ORM: Laravel Eloquent

---

## Core Features

### 1. Academic Discourse Engine
- Native support for Markdown and LaTeX formatting for complex scientific expression.
- Adjacency List Model implementation for infinitely threaded, structured comments and replies.
- Advanced search and topic-based tagging (Taxonomy) with filtering capabilities.

### 2. Immutable Reputation Ledger
- A transaction-tracking ledger that securely logs all reputation changes (deltas).
- Dynamic assignment of Academic Titles based on earned reputation points.
- Built-in anti-farming logic and composite database indexing to prevent vote manipulation.
- "Author's Pick" consensus mechanism for highlighting exceptional responses.

### 3. Real-Time Interactivity
- Instant, page-reload-free broadcasting of upvotes, comments, and moderation actions using Laravel Reverb.
- Granular notification preferences allowing users to control their alert subscriptions.

### 4. Advanced Content Moderation & RBAC
- Multi-tiered access control: Guest, Unverified, Verified, Moderator, and Administrator.
- Automated lexical filtering and spam link constraints.
- Dedicated Moderator Dashboard for reviewing user-reported content.
- Comprehensive Activity Logging (Audit Trail) for administrative oversight.

### 5. Privacy & Data Protection (GDPR Compliant)
- Strict requirement for email verification prior to platform interaction.
- Support for "Right to be Forgotten" allowing users to permanently delete or deactivate their accounts.
- Soft-delete implementation for critical entities to preserve academic discourse integrity when users depart.

---

## Installation & Setup

Follow these instructions to set up the KnowVerse platform in a local development environment.

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js & npm
- MySQL 8.x

### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/mohammedabusaada/knowverse
   cd knowverse
   ```

2. Install PHP and Node dependencies:
   ```bash
   composer install
   npm install
   ```

3. Environment Configuration:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database and Reverb settings in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=knowverse_db
   DB_USERNAME=root
   DB_PASSWORD=

   REVERB_APP_ID=your_app_id
   REVERB_APP_KEY=your_app_key
   REVERB_APP_SECRET=your_app_secret
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

5. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

6. Start the development servers (Run each in a separate terminal):
   ```bash
   php artisan serve
   php artisan reverb:start
   php artisan queue:work
   npm run dev
   ```

---

## Development Timeline (Agile/Scrum)

The project was developed over a 16-week period divided into 8 Sprints.

| Phase | Duration | Core Focus |
|-------|----------|------------|
| Sprint 1 - 2 | Weeks 1-4 | Requirements Gathering, System Design (UML/ERD). |
| Sprint 3 - 4 | Weeks 5-8 | Environment Setup, DB Architecture, Auth, Base UI. |
| Sprint 5 - 6 | Weeks 9-12 | Core Business Logic, Reputation Ledger, Laravel Reverb. |
| Sprint 7 - 8 | Weeks 13-16 | QA Testing, Security Validation, Documentation, Deployment. |

---

## Academic Team & Supervision

**Supervised by:** Ms. Amna Abu Khashiba

**Development Team:**
| Name | Academic ID |
|------|-------------|
| Mohammed Osama Abu Saada | 2141091009 |
| Heba Muslim Abu Amra | 2141091011 |
| Shatha Abu Basheer | 2141091046 |
| Dema Nasr Abu Zekri | 2141091025 |
| Reem Marwan Diab | 2141091014 |

---

## Academic Context

This project is submitted in fulfillment of the requirements for obtaining a Bachelor's Degree in Web Technology and Information Security (WIS) at the Computer Department.
Location: Palestine, Gaza.
Academic Year: 2025-2026.
