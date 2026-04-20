Security changes applied

1. Passwords
- Web registration now uses explicit bcrypt hashing with configurable rounds.
- Seeder accounts also use bcrypt.
- Web/API login will automatically rehash old password hashes when needed.

2. JWT for API and mobile
- API login now returns a signed JWT Bearer token.
- Protected API routes now use custom JWT middleware instead of Sanctum tokens.
- Use Authorization: Bearer <token> for /api/me and /api/dashboard/summary and other protected endpoints.
- Logout is stateless: remove the JWT on the client side.

3. Environment variables
Add these to .env in production:
JWT_SECRET=use-a-long-random-secret-here
JWT_TTL=120
BCRYPT_ROUNDS=12

Example API login response:
{
  "token_type": "Bearer",
  "token": "<jwt>",
  "expires_in_minutes": 120,
  "user": { ... }
}
