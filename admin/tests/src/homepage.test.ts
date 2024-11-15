import {beforeAll, describe, expect, it} from '@jest/globals';

const BASE_URL = 'http://localhost:7979';

describe('homepage', () => { 
    beforeAll(async () => {
        await page.goto(BASE_URL);
    });
    
    it('Should redirect to the login page', async () => {
        expect(page.url()).toBe(BASE_URL + "/login");
    });
});
