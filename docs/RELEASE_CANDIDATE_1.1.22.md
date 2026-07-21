# Alynt Account Gateway v1.1.22 Release Candidate

Date: 2026-07-21

## Scope

This candidate promotes the completed post-refactor pre-release corrections and consolidated end-to-end acceptance work recorded after public baseline v1.1.21. No production or staging site was changed while preparing this candidate.

## Source Gates

- Production build passed.
- POT generation completed with 1,166 strings and v1.1.22 project metadata.
- PHPCS passed.
- PHP syntax passed for 236 tracked PHP files.
- JavaScript syntax passed for 17 tracked JavaScript/MJS files.
- PHPUnit passed at 547 tests and 3,995 assertions.
- Reverse-order and fixed-random PHPUnit runs each passed at 547 tests and 3,995 assertions.
- npm audit reported zero vulnerabilities.
- Composer strict validation passed.
- Composer audit reported no security vulnerability advisories.
- `git diff --check` passed.

The LocalWP PHP configuration emits a non-blocking startup warning because its configured `php_imagick.dll` is absent from that runtime. OpenSSL, Composer validation, Composer audit, WordPress bootstrap, and plugin behavior all completed successfully.

## Package Inspection

- Candidate: `alynt-account-gateway-v1.1.22-final-rc.zip`
- SHA-256: `C873302210C2A3B49A2FFDAA56E3830433D5754F00F811BE5375098A6EBA16AE`
- Runtime files: 131
- Archive entries: 131 files under one `alynt-account-gateway/` root
- Path format: forward slashes only
- Development, test, source, vendor, build, map, Git, and documentation files: absent
- Header version, loaded constant, stable tag, and POT project version: 1.1.22
- GitHub updater header count: one
- Packaged runtime file hashes match the prepared source tree exactly.

An initial Windows archive was rejected before installation because its entry names contained backslashes. The final candidate above was rebuilt with normalized forward-slash names and passed the complete inspection.

## Plugin Tester Package Smoke

Confirmed target: LocalWP Plugin Tester at `http://plugin-tester.local/` (`plugin-tester local-only`).

- Installed the exact inspected candidate through WordPress `Plugin_Upgrader` with overwrite enabled.
- Fresh WordPress runtime reports plugin version 1.1.22.
- Plugin remained active at the same active-plugin position.
- Settings fingerprint remained `c801f9a23642ea7677725fd382864533f94b961dddaccf5076134b831f2c922e` before and after replacement.
- Database schema remained 0.1.8.
- All 131 installed files hash-match the inspected package.
- Branded login rendered successfully.
- Disabled registration rendered the expected Registration Unavailable screen.
- Anonymous My Account access returned safely to the branded login with a local `redirect_to` value.
- Browser console reported no errors during the route smoke.

## Decision

The v1.1.22 candidate received explicit publication approval and was released on 2026-07-21.

## Publication Closeout

- Release: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v1.1.22`
- Release commit: `f4e69b2b2c379fa337d1e35e07049dad1c249273`
- Build Release workflow: `https://github.com/NichlasB/alynt-account-gateway/actions/runs/29824642500`
- Public asset: `alynt-account-gateway-v1.1.22.zip`
- Public asset SHA-256: `529A22E112DFAEB625545637E34C124FD963F91345EDD73B06C4405015208CB0`
- Public package inspection: 131 runtime files, 142 total entries including directory entries, one root folder, forward-slash paths, no development content, and consistent v1.1.22 metadata.
- The public package's 131 runtime files match the approved candidate. The only byte-level difference was `readme.txt` line endings produced by Linux checkout versus the Windows candidate workspace; normalized content is identical.
- Alynt Plugin Updater on LocalWP Plugin Tester detected the public `1.1.21 -> 1.1.22` update and used the release asset URL.
- The updater installation preserved the plugin's active position and settings fingerprint.
- A fresh request reported version 1.1.22 and database schema 0.1.8; all 131 installed files hash-match the public asset.
- A final forced updater check reported current version 1.1.22, latest version 1.1.22, and no update offer.
- The branded login route rendered after the public updater installation with no browser console errors.

The v1.1.22 release cycle is complete. No staging or production site was changed during publication or updater verification.
