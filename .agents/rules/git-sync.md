# AUTOMATIC GIT PUSH & SYNC RULE

Whenever you complete ANY code change, bug fix, feature implementation, refactoring, test update, or documentation adjustment in this repository, you MUST ALWAYS:
1. Verify syntax and tests pass.
2. Stage and commit all changes with a clear conventional commit message:
   ```bash
   git add .
   git commit -m "<type>: <brief description>"
   ```
3. Immediately push to GitHub remote repository:
   ```bash
   git push origin main
   ```
4. Confirm `working tree clean` and `branch is up to date with 'origin/main'`.

Remote Repository: `https://github.com/vansangnina/khoepro` (origin / main).
