import { Page } from '@playwright/test';

export class LoginPage {
  constructor(public page: Page) { }

  async goto() {
    await this.page.goto('/login');
  }

  async login(username: string, password: string) {
    await this.page.fill('input[name="username"]', username);
    await this.page.fill('input[name="password"]', password);
    await this.page.click('button[name="登录"]');
  }
}