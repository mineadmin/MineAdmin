import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/loginPage.ts';

test.describe('登录模块', () => {
  test('正确账号可以登录', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.login('admin', '123456');
  });
});