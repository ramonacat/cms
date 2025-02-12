import {beforeEach, describe, expect, it} from '@jest/globals';

const BASE_URL = 'http://localhost:7979';

describe('login page', () => { 
    beforeEach(async () => {
        await page.goto(BASE_URL + "/login");
    });
    
    it('Should contain username and password fields', async () => {
        const usernameField = await page.$('[name=username]');
        expect(usernameField).not.toBeNull();

        const passwordField = await page.$('[name=password]');
        expect(passwordField).not.toBeNull();
    });
    
    it('Redirects to the home page after a succesful login', async () => {
        await page.type('[name=username]', 'testuser');
        await page.type('[name=password]', 'testpwd');
        
        await Promise.all([
            page.waitForNavigation(),
            page.click('button'),
        ]);

        expect(page.url()).toBe(BASE_URL + '/');
        expect((await page.content())).toContain('testuser');
    });

    it('Shows an error on invalid password', async () => {
        await page.type('[name=username]', 'testuser');
        await page.type('[name=password]', 'invalid password');
        
        await Promise.all([
            page.waitForNavigation(),
            page.click('button'),
        ]);

        expect(page.url()).toBe(BASE_URL + '/login');
        expect((await page.content())).toContain('Incorrect username or password');
    });
});
