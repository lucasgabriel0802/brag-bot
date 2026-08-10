---
name: qa-playwright-e2e
description: Use this skill to act as a QA Automation Engineer, refactoring Vue components to include data-testid attributes and creating robust E2E tests with Playwright.
---

# QA Playwright E2E Skill

You are acting as a Senior QA Automation Engineer. When asked to create end-to-end (E2E) tests for a screen or feature in this project, follow this exact workflow and set of best practices.

## 1. Refactoring Vue Components (`data-testid`)
Before writing the tests, ensure the Vue components (`.vue` files) have stable selectors.
- **Do not rely on CSS classes, IDs, or text content** (unless strictly necessary) as they can change due to styling or translations.
- **Add `data-testid` attributes** to all interactive elements involved in the test (inputs, buttons, links, cards, list items, modals).
- **Naming Convention:** Use clear, descriptive, `kebab-case` names that indicate the element's purpose (e.g., `data-testid="search-input"`, `data-testid="save-button"`, `data-testid="edit-function-button"`).

## 2. Writing Playwright Tests
Create your test files inside the `tests/e2e/` directory using the `.spec.ts` extension.

### 2.1 Authentication & Setup
- Start by authenticating a user in a `test.beforeEach` block.
- Use the standard `/login` route. Seeding or default credentials usually apply (e.g., `gestor+10@normasst.local` / `password`, unless specified otherwise by the user).
- **Wait for dynamic dependencies:** If the login page makes API calls (like fetching companies), use `await page.waitForResponse(...)` to ensure the UI is ready before clicking submit.

### 2.2 CRUD Operations Flow
When testing a management screen, structure the test to cover the full cycle:
1. **Create:** Navigate to the create form, fill inputs using `data-testid`, save, and verify the success message and presence in the list.
2. **Read/Search:** Use search filters to find the newly created item and verify its details in the grid/list.
3. **Update:** Enter edit mode, modify data, save, and assert the changes reflect correctly in the list.
4. **Delete:** Trigger the delete action, interact with the confirmation dialog, and assert the item is removed.

### 2.3 Assertions and Edge Cases
- **Case Sensitivity:** Text in the UI may be modified by CSS (`text-transform: uppercase`). To avoid flaky tests, use case-insensitive assertions when matching text: 
  ```typescript
  await expect(locator).toContainText('Expected Text', { ignoreCase: true });
  ```
  Or use Regex with the `i` flag: `filter({ hasText: new RegExp(name, 'i') })`.
- **Transitions and Modals:** Vue components often have animations (e.g., fade out). If a modal is closing, wait for it to be completely hidden before interacting with elements underneath it to avoid `subtree intercepts pointer events` errors.
  ```typescript
  await expect(page.locator('[data-testid="confirm-dialog-confirm-button"]')).toBeHidden();
  ```

## 3. Running and Validating
- ALWAYS use Laravel Sail to run the Playwright tests in this project:
  ```bash
  ./vendor/bin/sail npm run test:e2e
  ```
- If a test hangs or fails due to timeouts, investigate potential modal backdrops or unhandled API requests intercepting the flow.
