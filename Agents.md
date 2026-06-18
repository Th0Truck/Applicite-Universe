# Project instructions for Codex

## General
- This is a PHP project.
- Review code as a senior PHP backend developer.
- Prefer simple, readable, maintainable solutions over clever abstractions.
- Do not suggest rewrites unless the current implementation is risky or hard to maintain.
- Be strict about security, data integrity and backwards compatibility.

## PHP standards
- Follow PSR-12 coding style.
- Use strict typing where appropriate.
- Prefer typed properties, return types and parameter types.
- Avoid dynamic properties.
- Avoid hidden side effects in constructors.
- Avoid large methods and deeply nested conditionals.

## Laravel / Filament conventions
- Follow Laravel conventions for controllers, services, models, policies, requests and jobs.
- Validate input using Form Requests or explicit validation.
- Do not put business logic directly in controllers or Filament resources if it belongs in services/actions.
- Check authorization with policies, gates or middleware where relevant.
- Watch for N+1 queries and missing eager loading.
- Check migrations for safe defaults, nullable fields and rollback behavior.
- Check queued jobs for idempotency and retry safety.

## Security review
Pay special attention to:
- SQL injection
- XSS
- mass assignment
- missing authorization
- insecure file uploads
- unsafe deserialization
- leaking secrets or personal data
- weak validation
- trust in user-controlled input
- unsafe redirects
- missing CSRF protection

## Review output format
When reviewing code, respond in Danish.

Group findings by severity:

## Review checklist
When doing code reviews, also follow `docs/code_review.md`.

### Critical
Issues that can cause security breaches, data loss or production failure.

### High
Bugs, authorization problems, broken business logic or serious maintainability issues.

### Medium
Performance problems, missing tests, unclear structure or risky edge cases.

### Low
Style, naming, small refactors or readability improvements.

For each finding include:
- File and line/reference if possible
- Problem
- Why it matters
- Suggested fix
- Example code if useful

Do not list non-issues.
Do not praise the code unless asked.
If no serious issues are found, say that clearly and mention what was checked.