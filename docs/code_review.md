# PHP Code Review Checklist

## Correctness
- Does the code solve the intended problem?
- Are edge cases handled?
- Are null values handled explicitly?
- Are exceptions handled deliberately?

## Security
- Is all user input validated?
- Is authorization checked before data access or mutation?
- Are database queries parameterized or handled by Eloquent/query builder?
- Are file uploads validated by MIME type, extension and size?
- Are secrets excluded from logs and responses?

## Laravel
- Are controllers thin?
- Is business logic placed in services/actions/domain classes?
- Are Eloquent relations loaded efficiently?
- Are transactions used where multiple writes must succeed together?
- Are events/jobs idempotent?

## Tests
- Are critical paths covered by tests?
- Are authorization failures tested?
- Are validation failures tested?
- Are database changes tested?