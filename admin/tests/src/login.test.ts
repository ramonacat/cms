import {beforeAll, describe, expect, it} from '@jest/globals';

const BASE_URL = 'http://localhost:7979';

describe('homepage', () => { 
    beforeAll(async () => {
        await page.goto(BASE_URL + "/login");
    });
    
    it('Should contain username and password fields', async () => {
        const usernameField = await page.$('[name=username]');
        expect(usernameField).not.toBeNull();

        const passwordField = await page.$('[name=password]');
        expect(passwordField).not.toBeNull();
    });
});
