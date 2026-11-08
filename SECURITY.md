# Security Policy

SecureTS is an authentication product, so we take the security of this project and its users very seriously. Thank you for helping keep the project safe.

## Supported Versions

| Version | Supported |
| ------- | --------- |
| `main` (latest release) | ✅ |
| `develop`               | ✅ (best effort) |
| Older releases          | ❌ |

Security fixes go into `main` and are released as soon as possible.

## Reporting a Vulnerability

**Do not report security vulnerabilities through public GitHub issues, discussions, or pull requests.**

Report them privately instead through **GitHub Private Vulnerability Reporting**:

1. Go to the repository's **Security** tab.
2. Click **Report a vulnerability**.
3. Submit the advisory form.

Please include:

- The type of issue (e.g., authentication bypass, token leakage, XSS, SQL injection, IDOR, replay)
- The affected component (web, API, mobile) and the file paths, if known
- The affected branch, commit, or version
- Step-by-step reproduction instructions
- A proof of concept or exploit code, if available
- The impact, and how an attacker could exploit the issue

## Response Timeline

| Stage | Target |
| ----- | ------ |
| Acknowledgement of report | Within 72 hours |
| Initial assessment & severity | Within 7 days |
| Fix for critical/high severity | Within 30 days |
| Public disclosure | After a fix is released, coordinated with the reporter |

We will keep you informed throughout the process and credit you in the advisory unless you ask us not to.

## Scope

**In scope:**
- Authentication and approval flows (access requests, approvals, denials)
- TOTP generation and verification
- Setup tokens, Instance Tokens, and Integration API Secrets
- Sanctum API authentication and authorization
- Reverb broadcasting channels and authorization
- Mobile credential storage

**Out of scope:**
- Vulnerabilities in third-party dependencies that already have a public advisory (report these upstream)
- Denial-of-service attacks or volumetric testing
- Social engineering or physical attacks
- Findings from automated scanners with no demonstrated impact
- Missing security headers with no demonstrated exploit

## Safe Harbor

We will not pursue legal action against researchers who:

- Make a good-faith effort to avoid privacy violations, data destruction, and service disruption
- Access only their own accounts or test accounts
- Give us reasonable time to fix the issue before any disclosure
- Do not exploit a vulnerability beyond what is needed to demonstrate it

## Support

For general questions or bug reports that are **not** security-related, open a GitHub issue using the appropriate template.
