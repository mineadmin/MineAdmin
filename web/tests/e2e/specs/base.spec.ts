import { test, expect } from '@playwright/test';

test.describe.serial('Base Tests', () => {

  test('Api Test', async ({ request }) => {
    const response = await request.get('http://localhost:9501');

    expect(response.status()).toBe(200);

    expect(await response.text()).toEqual('welcome use mineAdmin');
  });

  test('Page Check', async ({ page }) => {
    await page.goto('/');

    await page.waitForLoadState('networkidle');

    await expect(page.getByRole('button', { name: '登录' })).toBeVisible();

    expect(await page.title()).toContain('MineAdmin');
  });

});
