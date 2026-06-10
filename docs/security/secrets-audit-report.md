# Secrets Audit Report

**Date:** 2026-06-08
**Auditor:** Automated (AI Agent)
**Scope:** All secrets, credentials, and sensitive data in repository

## Checks Performed

| Check                        | Status | Details                                                                                               |
| ---------------------------- | ------ | ----------------------------------------------------------------------------------------------------- |
| `.env` in `.gitignore`       | ✅     | `.env` is listed in `.gitignore`                                                                      |
| `.env` not tracked           | ✅     | `git ls-files` confirms `.env` not tracked                                                            |
| `.env` in git history        | ⚠️     | Found in commit ec0d2e1 (initial), removed in 135245a — content was non-sensitive (theme config only) |
| `.env.local` in history      | ✅     | Never committed                                                                                       |
| `.env.production` in history | ✅     | Never committed                                                                                       |
| `.pem`/`.key`/`.cert` files  | ✅     | None found in history                                                                                 |
| Hardcoded passwords in code  | ✅     | Only DDEV defaults (archtech/archtech, admin/admin) in config files — acceptable for local dev        |
| Secrets in CI logs           | ✅     | All secrets use `${{ secrets.* }}` in GitHub Actions                                                  |

## Findings

1. **No sensitive secrets exposed.** The historical `.env` commit contained only `CUSTOM_THEME_NAME` and `THEME_FRAMEWORK` — no passwords, tokens, or API keys.
2. **`.env` properly removed from tracking** in commit 135245a.
3. **`.env.example` expanded** with all required variables (production values omitted, marked with comments).
4. **CI secrets** are properly managed via GitHub Secrets / Vault — never visible in code.

## Recommendations

- [x] Keep `.env` in `.gitignore`
- [x] Use Vault for production secrets (see `infrastructure/vault/`)
- [x] Rotate secrets via Vault rotation scripts (`infrastructure/vault/scripts/`)
- [ ] Consider using `git filter-repo` to fully purge `.env` from git history for compliance (optional, as content was non-sensitive)
